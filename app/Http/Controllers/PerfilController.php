<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class PerfilController extends Controller
{
    public function index()
    {
        // Obtener usuario autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión.');
        }
    
        // Buscar el usuario en la tabla 'usuarios'
        $usuario = Usuario::where('id_usuario', Auth::id())->first();
    
        if (!$usuario) {
            return redirect()->route('login')->with('error', 'Usuario no encontrado.');
        }
    
        // Buscar cliente asociado en la base de datos y asegurar que esté actualizado
        $cliente = Cliente::where('id_usuario', $usuario->id_usuario)->first();
    
        if (!$cliente) {
            return redirect()->route('perfil')->with('error', 'Cliente no encontrado.');
        }
    
        // 🔥 Asegurar que el estado de la suscripción siempre es el más reciente
        $cliente->refresh();
    
        return view('perfil', compact('usuario', 'cliente'));
    }
    

    public function update(Request $request)
    {
        // Obtener usuario autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión.');
        }

        // Buscar el usuario en la tabla `usuarios`
        $usuario = Usuario::where('id_usuario', Auth::id())->first();

        if (!$usuario) {
            return back()->with('error', 'Usuario no encontrado.');
        }

        // Buscar el cliente relacionado
        $cliente = Cliente::where('id_usuario', $usuario->id_usuario)->first();

        // Validar los datos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'nombre_comercial' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:15',
        ]);

        // Actualizar datos del usuario
        $usuario->update([
            'nombre' => $request->nombre
        ]);

        // Actualizar datos del cliente
        if ($cliente) {
            $cliente->update([
                'nombre_comercial' => $request->nombre_comercial,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
            ]);
        }

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function renovar(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Usuario no autenticado'], 401);
        }
    
        $usuario = Usuario::where('id_usuario', Auth::id())->first();
    
        if (!$usuario) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }
    
        $cliente = Cliente::where('id_usuario', $usuario->id_usuario)->first();
    
        if (!$cliente) {
            return response()->json(['success' => false, 'message' => 'Cliente no encontrado'], 404);
        }
    
        // Verificar estado actual de la suscripción
        if (trim($cliente->estado_suscripcion) !== 'pendiente' && trim($cliente->estado_suscripcion) !== 'cancelado') {
            return response()->json([
                'success' => false,
                'message' => 'No puedes renovar una suscripción activa.'
            ]);
        }
    
        // Fecha de inicio y fin de suscripción
        $fechaInicio = now();
        $fechaFin = now()->addMonth();
    
        // Generar un ID único para la orden (si aún no tienes uno)
        $orderID = 'RENOV-' . uniqid();
    
        // 🔥 Registrar el pago en la tabla de pagos como una renovación
        DB::table('pagos_suscripcion')->insert([
            'id_cliente' => $cliente->id_cliente,
            'fecha_pago' => now(),
            'plan_suscripcion' => $cliente->plan_suscripcion, // Utiliza el plan del cliente
            'monto_pagado' => 300.00,  // Mismo monto que el primer pago
            'metodo_pago' => 'tarjeta', // Método de pago fijo (puedes cambiarlo según tu lógica)
            'referencia_pago' => $orderID,
            'estado_pago' => 'completado',
            'fecha_inicio_suscripcion' => $fechaInicio,
            'fecha_fin_suscripcion' => $fechaFin,
           
        ]);
    
        // 🔥 Actualizar el estado de la suscripción en el cliente
        $cliente->update([
            'estado_suscripcion' => 'activa',
            'fecha_fin_suscripcion' => $fechaFin
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Tu suscripción ha sido renovada correctamente.'
        ]);
    }
    
    
    
}
