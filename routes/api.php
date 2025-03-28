<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\AndroidController;




Route::prefix('mobile')->group(function () {
    Route::post('/registro', [AndroidController::class, 'registrar'])->name('movil.registro');

    Route::post('/login', [AndroidController::class, 'login']);
    Route::get('/clientes-activos', [AndroidController::class, 'obtenerClientesActivos']);
    Route::get('/clientes/{id}/sucursales', [AndroidController::class, 'obtenerSucursalesPorCliente']);

    Route::get('/sucursales/{id}/menus', [AndroidController::class, 'obtenerMenusPorSucursal']);
    Route::get('/menus/{id}/productos', [AndroidController::class, 'obtenerProductosPorMenu']);
    
    Route::post('/crear-pedido' , [AndroidController::class, 'crearPedido']);

    Route::post('/productos/carrito/detalles', [AndroidController::class, 'obtenerDetallesProductosCarrito']);
    Route::get('/pedidos/historial/{id_usuario}', [AndroidController::class, 'historialPedidos']);
    
    Route::post('/obtener-datos-pedido', [AndroidController::class, 'obtenerDatosPedido']);
    
    Route::post('/cambiar-contrasena', [AndroidController::class, 'cambiarContrasena']);

    Route::get('/clientes/{id_cliente}/usuarios', [AndroidController::class, 'getUsuariosAsociados']);

    Route::get('/negocio/{id_usuario}/historial-pedidos', [AndroidController::class, 'historialPedidosNegocio']);

    Route::post('/sucursales/{id_sucursal}/toggle', [AndroidController::class, 'toggleEstadoSucursal']);

    Route::get('/estadisticas/cliente/{id_usuario}', [AndroidController::class, 'obtenerEstadisticasCliente']);
    
    Route::get('/estadisticas/pedidos/{id_usuario}', [AndroidController::class, 'obtenerCantidadPedidosUsuario']);
    Route::put('/pedido/cancelar/{id}', [AndroidController::class, 'cancelarPedido']);


});
