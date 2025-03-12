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





use Illuminate\Support\Facades\Hash;

class AndroidController extends Controller
{
    /**
     * Método para autenticar al usuario
     */
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'email' => 'required|email',
            'contraseña' => 'required|string|min:8'
        ]);

        // Buscar al usuario por su email
        $usuario = Usuario::where('email', $request->email)->first();

        // Verificar si el usuario existe y la contraseña es correcta
        if (!$usuario || !Hash::check($request->contraseña, $usuario->contraseña)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Devolver el usuario autenticado
        return response()->json([
            'success' => true,
            'message' => 'Autenticación exitosa',
            'data' => $usuario
        ]);
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
            ->whereIn('sector', ['cafetería', 'restaurante'])
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
}
