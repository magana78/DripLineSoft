<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    /**
     * Mostrar la lista de pedidos del usuario autenticado.
     */
    public function index()
    {
        if (Auth::user()->rol === 'admin_cliente') {
            // Si el usuario es admin_cliente, mostrar TODOS los pedidos
            $pedidos = DB::table('pedidos')
                ->orderBy('fecha_pedido', 'desc')
                ->get();
        } else {
            // Si es un cliente final, mostrar solo sus pedidos
            $pedidos = DB::table('pedidos')
                ->where('id_usuario_cliente', Auth::id())
                ->orderBy('fecha_pedido', 'desc')
                ->get();
        }
    
        return view('dashboard.pedidos.index', compact('pedidos'));
    }
    
    /**
     * Mostrar los detalles de un pedido específico.
     */
   public function show($id)
{
    // Si el usuario es admin_cliente, mostrar cualquier pedido
    if (Auth::user()->rol === 'admin_cliente') {
        $pedido = DB::table('pedidos')
            ->where('id_pedido', $id)
            ->first();
    } else {
        // Si es cliente_final, solo mostrar sus propios pedidos
        $pedido = DB::table('pedidos')
            ->where('id_pedido', $id)
            ->where('id_usuario_cliente', Auth::id())
            ->first();
    }

    if (!$pedido) {
        return redirect()->route('pedidos.index')->with('error', 'Pedido no encontrado.');
    }

    // Calcular tiempo restante
    $fechaPedido = \Carbon\Carbon::parse($pedido->fecha_pedido);
    $horaEntrega = $fechaPedido->addMinutes($pedido->tiempo_entrega_estimado);
    $tiempoRestante = now()->diffInMinutes($horaEntrega, false);

    if ($tiempoRestante <= 0) {
        $pedido->tiempo_restante = "🚀 Puede pasar por su pedido 🚀";
        $pedido->tiempo_alerta = true;
    } else {
        $pedido->tiempo_restante = "$tiempoRestante minutos";
        $pedido->tiempo_alerta = false;
    }

    // Obtener el nombre correcto de la columna en la tabla productos
    $columnasProductos = DB::select("DESCRIBE productos");
    $nombreColumnaProducto = 'nombre';

    foreach ($columnasProductos as $columna) {
        if (in_array($columna->Field, ['nombre', 'nombre_producto', 'titulo', 'descripcion'])) {
            $nombreColumnaProducto = $columna->Field;
            break;
        }
    }

    // Obtener los detalles del pedido
    $detalles = DB::table('detalles_pedido')
        ->join('productos', 'detalles_pedido.id_producto', '=', 'productos.id_producto')
        ->select('detalles_pedido.*', "productos.$nombreColumnaProducto as nombre_producto")
        ->where('detalles_pedido.id_pedido', $id)
        ->get();

    return view('dashboard.pedidos.show', compact('pedido', 'detalles'));
}

    

    
    public function update(Request $request, $id)
{
    // Validar el estado recibido
    $request->validate([
        'estado' => 'required|in:pendiente,en preparación,listo,cancelado',
    ]);

    // Buscar el pedido
    $pedido = DB::table('pedidos')->where('id_pedido', $id)->first();

    if (!$pedido) {
        return redirect()->route('pedidos.index')->with('error', 'Pedido no encontrado.');
    }

    // Actualizar el estado del pedido
    DB::table('pedidos')->where('id_pedido', $id)->update([
        'estado' => $request->estado,
    ]);

    return redirect()->route('pedidos.show', $id)->with('success', 'Estado del pedido actualizado correctamente.');
}

public function cancel($id)
{
    // Buscar el pedido
    $pedido = DB::table('pedidos')->where('id_pedido', $id)->first();

    if (!$pedido) {
        return redirect()->route('pedidos.index')->with('error', 'Pedido no encontrado.');
    }

    // Actualizar el estado del pedido a "cancelado"
    DB::table('pedidos')->where('id_pedido', $id)->update([
        'estado' => 'cancelado',
    ]);

    return redirect()->route('pedidos.show', $id)->with('success', 'Pedido cancelado correctamente.');
}
public function updateTime(Request $request, $id)
{
    // Validar la entrada
    $request->validate([
        'tiempo_entrega_estimado' => 'required|integer|min:1',
    ]);

    // Buscar el pedido
    $pedido = DB::table('pedidos')->where('id_pedido', $id)->first();

    if (!$pedido) {
        return redirect()->route('pedidos.index')->with('error', 'Pedido no encontrado.');
    }

    // Actualizar el tiempo estimado de entrega
    DB::table('pedidos')->where('id_pedido', $id)->update([
        'tiempo_entrega_estimado' => $request->tiempo_entrega_estimado,
    ]);

    return redirect()->route('pedidos.show', $id)->with('success', 'Tiempo estimado de entrega actualizado correctamente.');
}



}