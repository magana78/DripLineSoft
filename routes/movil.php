<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovilUsuarioController;
use App\Http\Controllers\MovilPedidoController;
use App\Http\Controllers\MovilSucursalController;

Route::prefix('movil')->group(function () {
    
    // 📌 Rutas de usuario
    Route::post('/registro', [MovilUsuarioController::class, 'registrar'])->name('movil.registro');
    Route::post('/login', [MovilUsuarioController::class, 'login'])->name('movil.login');
    Route::get('/usuario/{id}', [MovilUsuarioController::class, 'obtenerUsuario'])->name('movil.usuario.obtener');

    // 📌 Rutas de pedidos
    Route::get('/pedidos/{id_usuario}', [MovilPedidoController::class, 'listarPedidos'])->name('movil.pedidos.listar');
    Route::post('/pedidos', [MovilPedidoController::class, 'crearPedido'])->name('movil.pedidos.crear');

    // 📌 Rutas de sucursales
    Route::get('/sucursales', [MovilSucursalController::class, 'listarSucursales'])->name('movil.sucursales.listar');
});
