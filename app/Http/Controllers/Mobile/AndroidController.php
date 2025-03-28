<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use App\Models\Cliente;
use App\Models\Sucursale;
use App\Models\Menu;
use App\Models\DetallesPedido;
use App\Models\Pedido;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class AndroidController extends Controller
{
    /**
     * Método para autenticar al usuario
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'contraseña' => 'required|string|min:8'
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if (!$usuario || !Hash::check($request->contraseña, $usuario->contraseña)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Autenticación exitosa',
            'data' => [
                'id_usuario' => $usuario->id_usuario,
                'nombre' => $usuario->nombre,
                'email' => $usuario->email,
                'rol' => $usuario->rol,
                'fecha_creacion' => $usuario->fecha_creacion, // Ahora siempre mostrará CST
            ]
        ]);
    }

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

    public function cambiarContrasena(Request $request)
    {
        Log::info("📩 Recibida solicitud de cambio de contraseña", $request->all());

        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuarios,id_usuario',
            'contrasena_actual' => 'required|string',
            'nueva_contrasena' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',       // Al menos una letra minúscula
                'regex:/[A-Z]/',       // Al menos una letra mayúscula
                'regex:/[0-9]/',       // Al menos un número
                'regex:/[@#\$%^&+=!]/' // Al menos un carácter especial
            ],
        ]);

        if ($validator->fails()) {
            Log::error("❌ Error de validación", $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Error de validación en los campos enviados',
                'errors' => $validator->errors()
            ], 400);
        }

        $usuario = Usuario::find($request->id_usuario);

        if (!$usuario) {
            Log::error("⚠ Usuario con ID {$request->id_usuario} no encontrado.");
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        Log::info("👤 Usuario encontrado: {$usuario->nombre} (ID: {$usuario->id_usuario})");

        if (!Hash::check($request->contrasena_actual, $usuario->getAuthPassword())) {
            Log::warning("⚠ Contraseña incorrecta para el usuario ID: {$usuario->id_usuario}");
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual no es correcta'
            ], 401);
        }

        try {
            $usuario->contraseña = Hash::make($request->nueva_contrasena);
            $usuario->save();

            Log::info("✅ Contraseña cambiada correctamente para el usuario ID: {$usuario->id_usuario}");

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error("🚨 Error al actualizar la contraseña: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }


    public function obtenerClientesActivos(): JsonResponse
    {
        $clientes = Cliente::select(
            'id_cliente',
            'id_usuario',
            'nombre_comercial',
            'logo',
            'sector',
            'estado_suscripcion'
        )
            ->where('estado_suscripcion', 'activa')
            ->whereIn('sector', ['cafetería', 'restaurante', 'otro'])
            ->get();

        // Verificar si se encontraron clientes
        if ($clientes->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron clientes activos en este momento.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Clientes activos encontrados.',
            'data' => $clientes
        ], 200);
    }

    public function obtenerSucursalesPorCliente($idCliente)
    {
        $cliente = Cliente::with('sucursales')->find($idCliente);

        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        // Convertir el valor de activa a booleano
        $sucursales = $cliente->sucursales->map(function ($sucursal) {
            $sucursal->activa = $sucursal->activa == 1; // Convierte 1 a true y 0 a false
            return $sucursal;
        });

        return response()->json([
            'success' => true,
            'data' => $sucursales
        ]);
    }

    public function obtenerMenusPorSucursal($idSucursal)
    {
        // Buscar la sucursal por ID y cargar sus menús
        $sucursal = Sucursale::with('menus')->find($idSucursal);

        // Verificar si la sucursal existe
        if (!$sucursal) {
            return response()->json([
                'success' => false,
                'message' => 'Sucursal no encontrada'
            ], 404);
        }

        // Verificar si tiene menús asociados
        $menus = $sucursal->menus;

        if ($menus->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron menús para esta sucursal',
                'data' => []
            ], 404);
        }

        // Responder con éxito
        return response()->json([
            'success' => true,
            'message' => 'Menús encontrados',
            'data' => $menus
        ], 200);
    }

    public function obtenerProductosPorMenu($idMenu)
    {
        // Buscar el menú por ID y cargar sus productos e imágenes
        $menu = Menu::with(['productos.imagenes_productos'])->find($idMenu);

        // Verificar si el menú existe
        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menú no encontrado'
            ], 404);
        }

        // Verificar si tiene productos asociados
        $productos = $menu->productos;

        if ($productos->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron productos para este menú',
                'data' => []
            ], 404);
        }

        // Responder con éxito
        return response()->json([
            'success' => true,
            'message' => 'Productos encontrados',
            'data' => $productos
        ], 200);
    }

    public function crearPedido(Request $request)
    {
        // Validar la información recibida
        $request->validate([
            'id_usuario_cliente' => 'required|integer',
            'metodo_pago' => 'required|string',
            'productos' => 'required|array',
            'productos.*.id_producto' => 'required|integer',  // Asegúrate que la clave es id_producto
            'productos.*.cantidad' => 'required|integer|min:1',
            'nota' => 'nullable|string',
            'descuento' => 'nullable|numeric|min:0'
        ]);

        try {
            // Obtener los productos con sus detalles
            $productosIds = collect($request->productos)->pluck('id_producto');  // Cambiar a id_producto
            $productosDisponibles = Producto::whereIn('id_producto', $productosIds)
                ->where('disponible', true)
                ->get();

            if ($productosDisponibles->count() !== count($request->productos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Uno o más productos no están disponibles o no existen.'
                ], 400);
            }

            // Determinar la sucursal a partir del primer producto
            $idSucursal = $productosDisponibles->first()->menu->id_sucursal;

            // Verificar que todos los productos pertenezcan a la misma sucursal
            if ($productosDisponibles->pluck('menu.id_sucursal')->unique()->count() > 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Todos los productos deben pertenecer a la misma sucursal.'
                ], 400);
            }

            // Obtener el cliente a partir de la sucursal
            $cliente = Sucursale::where('id_sucursal', $idSucursal)->first()->cliente;

            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró el cliente asociado a la sucursal.'
                ], 404);
            }

            // Insertar en la tabla cliente_usuario si no existe la asociación
            $usuarioAsociado = DB::table('cliente_usuario')
                ->where('id_cliente', $cliente->id_cliente)
                ->where('id_usuario', $request->id_usuario_cliente)
                ->exists();

            if (!$usuarioAsociado) {
                DB::table('cliente_usuario')->insert([
                    'id_cliente' => $cliente->id_cliente,
                    'id_usuario' => $request->id_usuario_cliente,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }


            // Iniciar transacción
            DB::beginTransaction();

            // Calcular el total del pedido
            $total = 0;
            foreach ($request->productos as $producto) {
                $precio = $productosDisponibles->firstWhere('id_producto', $producto['id_producto'])->precio;  // Cambiar a id_producto
                $subtotal = $producto['cantidad'] * $precio;
                $total += $subtotal;
            }

            // Aplicar descuento si existe
            if ($request->has('descuento')) {
                $total -= $request->descuento;
            }

            // Crear el pedido con la sucursal determinada
            $pedido = Pedido::create([
                'id_sucursal' => $idSucursal,
                'id_usuario_cliente' => $request->id_usuario_cliente,
                'fecha_pedido' => Carbon::now(),
                'metodo_pago' => $request->metodo_pago,
                'estado' => 'pendiente',
                'total' => $total,
                'descuento' => $request->descuento ?? 0,
                'nota' => $request->nota,
                'tiempo_entrega_estimado' => 30 // Tiempo estimado base
            ]);

            // Registrar los detalles del pedido
            foreach ($request->productos as $producto) {
                $precio = $productosDisponibles->firstWhere('id_producto', $producto['id_producto'])->precio;  // Cambiar a id_producto
                $subtotal = $producto['cantidad'] * $precio;

                DetallesPedido::create([
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $producto['id_producto'],  // Cambiar a id_producto
                    'cantidad' => $producto['cantidad'],
                    'subtotal' => $subtotal
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado con éxito',
                'data' => [
                    'id_pedido' => $pedido->id_pedido,
                    'total' => $total,
                    'tiempo_entrega_estimado' => $pedido->tiempo_entrega_estimado
                ]
            ], 201);
        } catch (\Exception $e) {
            // Capturar cualquier excepción y registrar detalles adicionales
            Log::error("Error al crear el pedido: " . $e->getMessage(), [
                'request' => $request->all(),
                'stack' => $e->getTraceAsString()
            ]);

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el pedido: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString()  // Incluir detalles del error en la respuesta
            ], 500);
        }
    }

    public function obtenerDetallesProductosCarrito(Request $request)
    {
        $request->validate([
            'productos' => 'required|array',
            'productos.*.idProducto' => 'required|integer'
        ]);

        $productosIds = collect($request->productos)->pluck('idProducto');

        $productos = Producto::whereIn('id_producto', $productosIds)->get();

        if ($productos->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontraron productos'
            ], 404);
        }

        // Estructura del nuevo objeto de respuesta
        return response()->json([
            'success' => true,
            'message' => 'Detalles de productos encontrados',
            'data' => $productos
        ], 200);
    }

    public function obtenerDatosPedido(Request $request)
    {
        try {
            // Validar el request
            $request->validate([
                'productos' => 'required|array',
                'productos.*' => 'integer|exists:productos,id_producto'
            ]);

            // Obtener productos
            $productos = Producto::with([
                'menu.sucursale.cliente'
            ])->whereIn('id_producto', $request->productos)->get();

            // Si no hay productos encontrados
            if ($productos->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron datos para los productos proporcionados.'
                ], 404);
            }

            // Obtener datos del primer producto para obtener información del negocio
            $primerProducto = $productos->first();

            // Validar que las relaciones existan para evitar errores
            $datosNegocio = [
                'nombre_comercial' => $primerProducto->menu->sucursale->cliente->nombre_comercial ?? 'No disponible',
                'nombre_sucursal' => $primerProducto->menu->sucursale->nombre_sucursal ?? 'No disponible',
                'nombre_menu' => $primerProducto->menu->nombre_menu ?? 'No disponible',
                'logo_cliente' => $primerProducto->menu->sucursale->cliente->logo ?? null,
            ];


            return response()->json([
                'success' => true,
                'datos_negocio' => $datosNegocio
            ]);
        } catch (\Exception $e) {
            // Manejar errores inesperados para siempre retornar un JSON

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado al obtener los datos del negocio.',
                'error' => $e->getMessage()  // Para depuración, elimina esto en producción
            ], 500);
        }
    }

    public function getUsuariosAsociados($id_cliente)
    {
        // Buscar el cliente por ID
        $cliente = Cliente::with('usuarios_asociados')->find($id_cliente);

        // Si no se encuentra el cliente, devolver error
        if (!$cliente) {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        // Devolver la lista de usuarios asociados
        return response()->json([
            'success' => true,
            'data' => $cliente->usuarios_asociados
        ], 200);
    }


    public function toggleEstadoSucursal($id_sucursal)
    {
        $sucursal = Sucursale::find($id_sucursal);

        if (!$sucursal) {
            return response()->json(['exito' => false, 'mensaje' => 'Sucursal no encontrada'], 404);
        }

        $sucursal->activa = !$sucursal->activa;
        $sucursal->save();

        return response()->json([
            'exito' => true,
            'estado' => $sucursal->activa,
            'mensaje' => 'Sucursal actualizada correctamente'
        ]);
    }

    // 📌 Obtener cantidad de sucursales, menús y productos por cliente (negocio)
    public function obtenerEstadisticasCliente($id_usuario)
    {
        try {
            // Obtener el cliente relacionado al usuario
            $cliente = Cliente::where('id_usuario', $id_usuario)->first();

            if (!$cliente) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Cliente no encontrado'
                ], 404);
            }

            // Obtener cantidades
            $cantidadSucursales = $cliente->sucursales()->count();
            $cantidadMenus = $cliente->sucursales()->withCount('menus')->get()->sum('menus_count');
            $cantidadProductos = $cliente->sucursales()->with('menus.productos')->get()->sum(function ($sucursal) {
                return $sucursal->menus->sum(function ($menu) {
                    return $menu->productos->count();
                });
            });

            return response()->json([
                'exito' => true,
                'cantidadSucursales' => $cantidadSucursales,
                'cantidadMenus' => $cantidadMenus,
                'cantidadProductos' => $cantidadProductos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener estadísticas del cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 📌 Obtener cantidad de pedidos por usuario
    public function obtenerCantidadPedidosUsuario($id_usuario)
    {
        try {
            $cantidadPedidos = Pedido::where('id_usuario_cliente', $id_usuario)->count();

            return response()->json([
                'exito' => true,
                'cantidadPedidos' => $cantidadPedidos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener la cantidad de pedidos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 📌 5️⃣ Historial de pedidos de un usuario con detalles correctos
    public function historialPedidos($id_usuario)
    {
        try {
            $pedidos = Pedido::where('id_usuario_cliente', $id_usuario)
                ->with([
                    'detalles_pedidos.producto',
                    'sucursale',
                    'sucursale.cliente',
                ])
                ->get();

            if ($pedidos->isEmpty()) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'No hay pedidos en el historial'
                ], 404);
            }

            $pedidosFormateados = $pedidos->map(function ($pedido) {
                return [
                    'id_pedido' => $pedido->id_pedido,
                    'nombre_comercial' => optional($pedido->sucursale->cliente)->nombre_comercial ?? 'No disponible',
                    'nombre_sucursal' => optional($pedido->sucursale)->nombre_sucursal ?? 'No disponible',
                    'fecha_pedido' => $pedido->fecha_pedido->format('Y-m-d H:i:s'),
                    'fecha_entregado' => $pedido->fecha_entregado
                        ? $pedido->fecha_entregado->format('Y-m-d H:i:s')
                        : 'No entregado',
                    'metodo_pago' => $pedido->metodo_pago,
                    'estado' => $pedido->estado,
                    'total' => $pedido->total,
                    'descuento' => $pedido->descuento ?? 0,
                    'nota' => $pedido->nota,
                    'tiempo_entrega_estimado' => $pedido->tiempo_entrega_estimado,
                    'detalles' => $pedido->detalles_pedidos->map(function ($detalle) {
                        return [
                            'id_detalle' => $detalle->id_detalle,
                            'id_producto' => $detalle->id_producto,
                            'nombre_producto' => optional($detalle->producto)->nombre_producto ?? 'No disponible',
                            'cantidad' => $detalle->cantidad,
                            'subtotal' => $detalle->subtotal,
                        ];
                    })
                ];
            });

            return response()->json([
                'exito' => true,
                'pedidos' => $pedidosFormateados
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener el historial de pedidos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function historialPedidosNegocio($id_usuario)
    {
        try {
            // Buscar el cliente asociado al usuario
            $cliente = Cliente::where('id_usuario', $id_usuario)
                ->with('sucursales.pedidos.detalles_pedidos.producto')
                ->first();

            // Si el cliente no existe, retornar error
            if (!$cliente) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'No se encontró un negocio asociado a este usuario'
                ], 404);
            }

            // Obtener los pedidos de todas las sucursales del negocio
            $pedidos = collect();
            foreach ($cliente->sucursales as $sucursal) {
                foreach ($sucursal->pedidos as $pedido) {
                    $pedidos->push([
                        'id_pedido' => $pedido->id_pedido,
                        'nombre_comercial' => $cliente->nombre_comercial,
                        'nombre_sucursal' => $sucursal->nombre_sucursal,
                        'fecha_pedido' => $pedido->fecha_pedido->format('Y-m-d H:i:s'),
                        'fecha_entregado' => $pedido->fecha_entregado
                            ? $pedido->fecha_entregado->format('Y-m-d H:i:s')
                            : 'No entregado',
                        'metodo_pago' => $pedido->metodo_pago,
                        'estado' => $pedido->estado,
                        'total' => $pedido->total,
                        'descuento' => $pedido->descuento ?? 0,
                        'nota' => $pedido->nota,
                        'tiempo_entrega_estimado' => $pedido->tiempo_entrega_estimado,
                        'detalles' => $pedido->detalles_pedidos->map(function ($detalle) {
                            return [
                                'id_detalle' => $detalle->id_detalle,
                                'id_producto' => $detalle->id_producto,
                                'nombre_producto' => optional($detalle->producto)->nombre_producto ?? 'No disponible',
                                'cantidad' => $detalle->cantidad,
                                'subtotal' => $detalle->subtotal,
                            ];
                        })
                    ]);
                }
            }

            if ($pedidos->isEmpty()) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'No hay pedidos en el historial'
                ], 404);
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
}
