<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class MovilUsuarioController extends Controller
{
    // 📌 Registrar usuario
    public function registrar(Request $request)
    {
        try {
            // ✅ Validación de datos con JSON response
            $request->validate([
                'nombre' => 'required|string',
                'email' => 'required|email|unique:usuarios',
                'password' => 'required|min:8'
            ]);

            // ✅ Crear usuario con contraseña encriptada
            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'contraseña' => Hash::make($request->password),
                'rol' => 'cliente_final',
                'fecha_creacion' => now()
            ]);

            // ✅ Devolver JSON con mensaje de éxito
            return response()->json([
                'exito' => true,
                'mensaje' => 'Usuario registrado exitosamente',
                'usuario' => $usuario
            ], 201); // Código HTTP 201 (Creado)

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    // 📌 Iniciar sesión
    public function login(Request $request)
    {
        try {
            // ✅ Buscar usuario por email
            $usuario = Usuario::where('email', $request->email)->first();

            // ❌ Verificar si el usuario no existe o la contraseña es incorrecta
            if (!$usuario || !Hash::check($request->password, $usuario->contraseña)) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Credenciales incorrectas'
                ], 401); // Código HTTP 401 (No autorizado)
            }

            // ✅ Devolver JSON con mensaje de éxito
            return response()->json([
                'exito' => true,
                'mensaje' => 'Inicio de sesión exitoso',
                'usuario' => $usuario
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    // 📌 Obtener usuario por ID
    public function obtenerUsuario($id)
    {
        try {
            // ✅ Buscar usuario con relaciones
            $usuario = Usuario::with('clientes', 'pedidos')->find($id);

            // ❌ Si el usuario no existe, devolver error
            if (!$usuario) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Usuario no encontrado'
                ], 404);
            }

            // ✅ Devolver JSON con usuario encontrado
            return response()->json([
                'exito' => true,
                'usuario' => $usuario
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}
