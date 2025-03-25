<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Mail\PedidoEntregadoCanceladoMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PedidoController extends Controller
{
    public function index()
    {
        if (Auth::user()->rol === 'admin_cliente') {
            $pedidos = DB::table('pedidos')
                ->orderBy('fecha_pedido', 'desc')
                ->get();
        } else {
            $pedidos = DB::table('pedidos')
                ->where('id_usuario_cliente', Auth::id())
                ->orderBy('fecha_pedido', 'desc')
                ->get();
        }
    
        return view('dashboard.pedidos.index', compact('pedidos'));
    }

    public function show($id)
    {
        $pedido = \App\Models\Pedido::with('cliente')->find($id);
    
        if (!$pedido) {
            return redirect()->route('pedidos.index')->with('error', 'Pedido no encontrado.');
        }
    
        // Verificar si el pedido está cancelado o entregado para ocultar las opciones
        $ocultarOpciones = in_array($pedido->estado, ['entregado', 'cancelado']);
    
        // Calcular el tiempo restante
        $fechaPedido = Carbon::parse($pedido->fecha_pedido);
        $horaEntrega = $fechaPedido->addMinutes($pedido->tiempo_entrega_estimado);
        $tiempoRestante = now()->diffInMinutes($horaEntrega, false);
    
        $pedido->tiempo_restante = $tiempoRestante <= 0 ? "🚀 Puede pasar por su pedido 🚀" : "$tiempoRestante minutos";
    
        // Obtener detalles del pedido
        $detalles = DB::table('detalles_pedido')
            ->join('productos', 'detalles_pedido.id_producto', '=', 'productos.id_producto')
            ->select('detalles_pedido.*', 'productos.nombre_producto')
            ->where('detalles_pedido.id_pedido', $id)
            ->get();
    
        return view('dashboard.pedidos.show', compact('pedido', 'detalles', 'ocultarOpciones'));
    }
    

    public function update(Request $request, $id)
{
    $request->validate([
        'estado' => 'required|in:pendiente,en preparación,listo,cancelado,entregado',
    ]);

    $pedido = \App\Models\Pedido::find($id);

    if (!$pedido) {
        return redirect()->route('pedidos.index')->with('error', 'Pedido no encontrado.');
    }

    $estado = $request->estado;
    $pedido->estado = $estado;

    if ($estado == 'entregado') {
        $pedido->fecha_entregado = now();
        $pedido->save();

        // Obtener el cliente asociado al pedido
        $cliente = DB::table('clientes')
            ->where('id_usuario', $pedido->id_usuario_cliente)
            ->first();

        if ($cliente && !empty($cliente->email_contacto)) {
            try {
                Mail::to($cliente->email_contacto)->send(new PedidoEntregadoCanceladoMail($pedido, 'entregado'));
                Log::info('Correo enviado a: ' . $cliente->email_contacto);
            } catch (\Exception $e) {
                Log::error('Error al enviar el correo: ' . $e->getMessage());
                return redirect()->route('pedidos.show', $id)->with('error', 'El correo no pudo ser enviado.');
            }
        } else {
            return redirect()->route('pedidos.show', $id)->with('error', 'El cliente no tiene un correo electrónico registrado.');
        }
    } else {
        $pedido->save();
    }

    return redirect()->route('pedidos.show', $id)->with('success', 'Estado del pedido actualizado correctamente.');
}



    // public function cancel($id)
    // {
    //     $pedido = DB::table('pedidos')->where('id_pedido', $id)->first();

    //     if (!$pedido || in_array($pedido->estado, ['entregado', 'cancelado'])) {
    //         return redirect()->route('pedidos.show', $id)->with('error', 'No se puede cancelar un pedido ya entregado o cancelado.');
    //     }

    //     DB::table('pedidos')->where('id_pedido', $id)->update(['estado' => 'cancelado']);
    //     return redirect()->route('pedidos.show', $id)->with('success', 'Pedido cancelado correctamente.');
    // }

    public function updateTime(Request $request, $id)
    {
        $request->validate([
            'tiempo_entrega_estimado' => 'required|integer|min:1',
        ]);

        $pedido = DB::table('pedidos')->where('id_pedido', $id)->first();

        if (!$pedido) {
            return redirect()->route('pedidos.index')->with('error', 'Pedido no encontrado.');
        }

        DB::table('pedidos')->where('id_pedido', $id)->update([
            'tiempo_entrega_estimado' => $request->tiempo_entrega_estimado,
        ]);

        return redirect()->route('pedidos.show', $id)->with('success', 'Tiempo estimado de entrega actualizado correctamente.');
    }

 
    
    public function entregar(Request $request, $id)
    {
        $request->validate([
            'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia',
            'monto_recibido' => 'required|numeric|min:' . ($request->monto_recibido - $request->descuento)
        ]);
    
        $pedido = \App\Models\Pedido::findOrFail($id);
    
        if ($pedido->estado == 'entregado' || $pedido->estado == 'cancelado') {
            return response()->json(['error' => 'Este pedido ya fue entregado o cancelado.'], 400);
        }
    
        $pedido->estado = 'entregado';
        $pedido->fecha_entregado = now();
        $pedido->save();
    
            // ✅ Deshabilitar botón de pago después del pago
    session(['botonPagarDeshabilitado' => true]);
                    
        $totalAPagar = $pedido->total - $pedido->descuento;
        $cambioDevuelto = $request->monto_recibido - $totalAPagar;
    
        $cliente = DB::table('clientes')
            ->where('id_usuario', $pedido->id_usuario_cliente)
            ->first();
    
        if ($cliente && !empty($cliente->email_contacto)) {
            try {
                Mail::to($cliente->email_contacto)->send(new PedidoEntregadoCanceladoMail($pedido, 'entregado'));
                Log::info('Correo enviado a: ' . $cliente->email_contacto);
            } catch (\Exception $e) {
                Log::error('Error al enviar el correo: ' . $e->getMessage());
                return response()->json([
                    'error' => 'El correo de confirmación no pudo ser enviado.'
                ], 500);
            }
        } else {
            return response()->json([
                'error' => 'El cliente no tiene un correo electrónico registrado.'
            ], 400);
        }
    
        return response()->json([
            'success' => 'Pedido entregado correctamente. Se ha enviado un correo de confirmación.',
            'cambio_devuelto' => $cambioDevuelto
        ]);
    }
    
    
    
    public function marcarComoListo($id)
{
    $pedido = \App\Models\Pedido::findOrFail($id);

    if ($pedido->estado == 'listo') {
        $cliente = DB::table('clientes')
            ->where('id_usuario', $pedido->id_usuario_cliente)
            ->first();

        if ($cliente && !empty($cliente->email_contacto)) {
            try {
                Mail::to($cliente->email_contacto)->send(new PedidoEntregadoCanceladoMail($pedido, 'listo'));
                Log::info('Correo de pedido listo reenviado a: ' . $cliente->email_contacto);
                return redirect()->route('pedidos.show', $id)->with('success', 'El pedido ya estaba marcado como listo, pero se reenvió el correo de notificación.');
            } catch (\Exception $e) {
                Log::error('Error al enviar el correo: ' . $e->getMessage());
                return redirect()->route('pedidos.show', $id)->with('error', 'El correo de confirmación no pudo ser enviado.');
            }
        } else {
            return redirect()->route('pedidos.show', $id)->with('error', 'El cliente no tiene un correo electrónico registrado.');
        }
    }

    session(['ocultarOpciones' => true]);

    // Marcar como "listo"
    $pedido->estado = 'listo';
    $pedido->save();

    $cliente = DB::table('clientes')
        ->where('id_usuario', $pedido->id_usuario_cliente)
        ->first();

    if ($cliente && !empty($cliente->email_contacto)) {
        try {
            Mail::to($cliente->email_contacto)->send(new PedidoEntregadoCanceladoMail($pedido, 'listo'));
            Log::info('Correo de pedido listo enviado a: ' . $cliente->email_contacto);
        } catch (\Exception $e) {
            Log::error('Error al enviar el correo: ' . $e->getMessage());
            return redirect()->route('pedidos.show', $id)->with('error', 'El correo de confirmación no pudo ser enviado.');
        }
    } else {
        return redirect()->route('pedidos.show', $id)->with('error', 'El cliente no tiene un correo electrónico registrado.');
    }

    return redirect()->route('pedidos.show', $id)->with('success', 'Pedido marcado como listo y correo de notificación enviado.');
}

    

    

    
public function cancel($id)
{
    $pedido = \App\Models\Pedido::findOrFail($id);

    if ($pedido->estado == 'entregado' || $pedido->estado == 'cancelado') {
        return redirect()->route('pedidos.show', $id)->with('error', 'No se puede cancelar un pedido ya entregado o cancelado.');
    }

    $pedido->estado = 'cancelado';
    $pedido->save();

    session()->flash('ocultar_opciones', true);

    $cliente = DB::table('clientes')
        ->where('id_usuario', $pedido->id_usuario_cliente)
        ->first();

    if ($cliente && !empty($cliente->email_contacto)) {
        try {
            Mail::to($cliente->email_contacto)->send(new PedidoEntregadoCanceladoMail($pedido, 'cancelado'));
            Log::info('Correo de cancelación enviado a: ' . $cliente->email_contacto);
        } catch (\Exception $e) {
            Log::error('Error al enviar el correo: ' . $e->getMessage());
            return redirect()->route('pedidos.show', $id)->with('error', 'El correo de cancelación no pudo ser enviado.');
        }
    } else {
        return redirect()->route('pedidos.show', $id)->with('error', 'El cliente no tiene un correo electrónico registrado.');
    }

    return redirect()->route('pedidos.show', $id)->with('success', 'Pedido cancelado correctamente y correo de notificación enviado.');
}

    
    


}
