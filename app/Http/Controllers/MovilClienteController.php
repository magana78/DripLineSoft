<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Validator;

class MovilClienteController extends Controller
{
    // 📌 1️⃣ Registrar un nuevo cliente
    public function registrar(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_usuario' => 'required|integer|exists:usuarios,id_usuario',
                'nombre_comercial' => 'required|string|max:255',
                'direccion' => 'required|string',
                'telefono' => 'required|string|max:20',
                'email_contacto' => 'required|email|unique:clientes,email_contacto',
                'plan_suscripcion' => 'required|in:mensual,anual',
                'sector' => 'required|in:cafeteria,restaurante,otro'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Error en la validación de datos.',
                    'errores' => $validator->errors()
                ], 422);
            }

            $cliente = Cliente::create([
                'id_usuario' => $request->id_usuario,
                'nombre_comercial' => $request->nombre_comercial,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'email_contacto' => $request->email_contacto,
                'plan_suscripcion' => $request->plan_suscripcion,
                'fecha_registro' => now(),
                'sector' => $request->sector
            ]);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Cliente registrado exitosamente.',
                'cliente' => $cliente
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al registrar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    // 📌 2️⃣ Obtener información de un cliente por ID
    public function obtenerCliente($id)
    {
        try {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Cliente no encontrado.'
                ], 404);
            }

            return response()->json([
                'exito' => true,
                'mensaje' => 'Cliente encontrado correctamente.',
                'cliente' => $cliente
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    // 📌 3️⃣ Actualizar información de un cliente
    public function actualizar(Request $request, $id)
    {
        try {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Cliente no encontrado.'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre_comercial' => 'string|max:255',
                'direccion' => 'string',
                'telefono' => 'string|max:20',
                'email_contacto' => 'email|unique:clientes,email_contacto,' . $id . ',id_cliente',
                'plan_suscripcion' => 'in:mensual,anual',
                'sector' => 'in:cafeteria,restaurante,otro'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Error en la validación de datos.',
                    'errores' => $validator->errors()
                ], 422);
            }

            $cliente->update($request->all());

            return response()->json([
                'exito' => true,
                'mensaje' => 'Cliente actualizado correctamente.',
                'cliente' => $cliente
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al actualizar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    // 📌 4️⃣ Listar todos los clientes
    public function listarClientes()
    {
        try {
            $clientes = Cliente::all();

            if ($clientes->isEmpty()) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'No hay clientes disponibles.'
                ], 404);
            }

            return response()->json([
                'exito' => true,
                'mensaje' => 'Lista de clientes obtenida correctamente.',
                'clientes' => $clientes
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener los clientes: ' . $e->getMessage()
            ], 500);
        }
    }
}
