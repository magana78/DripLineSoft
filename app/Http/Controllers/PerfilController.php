<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Usuario;

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
    
        // Verificar estado actual
        if (trim($cliente->estado_suscripcion) !== 'pendiente' && trim($cliente->estado_suscripcion) !== 'cancelado') {
            return response()->json([
                'success' => false,
                'message' => 'No puedes renovar una suscripción activa'
            ]);
        }
    
        // 🔥 Actualizar en la base de datos
        $cliente->update(['estado_suscripcion' => 'activa']);
    
        return response()->json([
            'success' => true,
            'message' => 'Tu suscripción ha sido activada correctamente.'
        ]);
    }
    
    
}
