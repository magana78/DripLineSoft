<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\HelloController;
use App\Http\Controllers\Mobile\AndroidController;
use App\Http\Controllers\MovilUsuarioController;




Route::prefix('mobile')->group(function () {
    Route::get('/hello', [HelloController::class, 'index']);
    Route::post('/registro', [MovilUsuarioController::class, 'registrar'])->name('movil.registro');

    Route::post('/login', [AndroidController::class, 'login']);
    Route::get('/clientes-activos', [AndroidController::class, 'obtenerClientesActivos']);
    Route::get('/clientes/{id}/sucursales', [AndroidController::class, 'obtenerSucursalesPorCliente']);

    Route::get('/sucursales/{id}/menus', [AndroidController::class, 'obtenerMenusPorSucursal']);
    Route::get('/menus/{id}/productos', [AndroidController::class, 'obtenerProductosPorMenu']);
    
    Route::post('/crear-pedido' , [AndroidController::class, 'crearPedido']);

    Route::post('/productos/carrito/detalles', [AndroidController::class, 'obtenerDetallesProductosCarrito']);



});
