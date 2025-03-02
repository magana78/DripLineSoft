<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\DetallesPedido;
use App\Models\Producto;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MovilPedidoController extends Controller
{
    // 📌 1️⃣ Crear un nuevo pedido
    public function crearPedido(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_usuario_cliente' => 'required|integer|exists:usuarios,id_usuario',
            'id_sucursal' => 'required|integer|exists:sucursales,id_sucursal',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
            'productos' => 'required|array|min:1',
            'productos.*.id_producto' => 'required|integer|exists:productos,id_producto',
            'productos.*.cantidad' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json(['exito' => false, 'errores' => $validator->errors()], 422);
        }

        try {
            // Crear el pedido
            $pedido = Pedido::create([
                'id_usuario_cliente' => $request->id_usuario_cliente,
                'id_sucursal' => $request->id_sucursal,
                'metodo_pago' => $request->metodo_pago,
                'fecha_pedido' => now(),
                'estado' => 'pendiente',
                'total' => 0,
                'descuento' => 0
            ]);

            $total = 0;
            $productosPedidos = [];

            foreach ($request->productos as $producto) {
                $item = Producto::find($producto['id_producto']);
                if (!$item) {
                    return response()->json(['exito' => false, 'mensaje' => 'Producto no encontrado'], 404);
                }

                $subtotal = $item->precio * $producto['cantidad'];
                $detallePedido = DetallesPedido::create([
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $producto['id_producto'],
                    'cantidad' => $producto['cantidad'],
                    'subtotal' => $subtotal
                ]);

                $productosPedidos[] = $detallePedido;
                $total += $subtotal;
            }

            // Actualizar total del pedido
            $pedido->update(['total' => $total]);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Pedido creado exitosamente',
                'pedido' => $pedido->load('detalles_pedidos.producto')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al crear el pedido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 📌 2️⃣ Listar pedidos activos de un usuario
    public function listarPedidosActivos($id_usuario)
    {
        try {
            $pedidos = Pedido::where('id_usuario_cliente', $id_usuario)
                ->whereIn('estado', ['pendiente', 'en preparación'])
                ->with('detalles_pedidos.producto')
                ->get();

            if ($pedidos->isEmpty()) {
                return response()->json(['exito' => false, 'mensaje' => 'No hay pedidos activos'], 404);
            }

            return response()->json([
                'exito' => true,
                'pedidos' => $pedidos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al listar pedidos activos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 📌 3️⃣ Obtener detalles de un pedido
    public function obtenerPedido($id)
    {
        try {
            $pedido = Pedido::with('detalles_pedidos.producto')->findOrFail($id);

            return response()->json([
                'exito' => true,
                'pedido' => $pedido
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['exito' => false, 'mensaje' => 'Pedido no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener el pedido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 📌 4️⃣ Cancelar un pedido
    public function cancelarPedido($id)
    {
        try {
            $pedido = Pedido::findOrFail($id);

            if ($pedido->estado != 'pendiente') {
                return response()->json(['exito' => false, 'mensaje' => 'No se puede cancelar este pedido'], 400);
            }

            $pedido->update(['estado' => 'cancelado']);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Pedido cancelado correctamente'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['exito' => false, 'mensaje' => 'Pedido no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al cancelar el pedido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 📌 5️⃣ Historial de pedidos de un usuario
    public function historialPedidos($id_usuario)
    {
        try {
            $pedidos = Pedido::where('id_usuario_cliente', $id_usuario)
                ->whereNotIn('estado', ['pendiente', 'en preparación'])
                ->with('detalles_pedidos.producto')
                ->get();

            if ($pedidos->isEmpty()) {
                return response()->json(['exito' => false, 'mensaje' => 'No hay pedidos en el historial'], 404);
            }

            return response()->json([
                'exito' => true,
                'pedidos' => $pedidos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener el historial de pedidos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
