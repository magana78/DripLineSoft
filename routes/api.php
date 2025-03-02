<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovilUsuarioController;
use App\Http\Controllers\MovilPedidoController;
use App\Http\Controllers\MovilSucursalController;
use App\Http\Controllers\MovilClienteController;
use App\Http\Controllers\MovilRestauranteController;

Route::prefix('movil')->group(function () {
    
    // 📌 Rutas de usuario
    Route::post('/registro', [MovilUsuarioController::class, 'registrar'])->name('movil.registro');
    Route::post('/login', [MovilUsuarioController::class, 'login'])->name('movil.login');
    Route::get('/usuario/{id}', [MovilUsuarioController::class, 'obtenerUsuario'])->name('movil.usuario.obtener');

    // 📌 Rutas de pedidos
    Route::post('/pedido', [MovilPedidoController::class, 'crearPedido']);
    Route::get('/pedido/{id}', [MovilPedidoController::class, 'obtenerPedido']);
    Route::get('/pedidos/activos/{id_usuario}', [MovilPedidoController::class, 'listarPedidosActivos']);
    Route::put('/pedido/cancelar/{id}', [MovilPedidoController::class, 'cancelarPedido']);
    Route::get('/pedidos/historial/{id_usuario}', [MovilPedidoController::class, 'historialPedidos']);


    // 📌 Rutas de sucursales
    Route::get('/sucursales', [MovilSucursalController::class, 'listarSucursales'])->name('movil.sucursales.listar');

    // 📌 Rutas de clientes
    Route::post('/clientes', [MovilClienteController::class, 'registrar'])->name('movil.clientes.registrar');
    Route::get('/clientes/{id}', [MovilClienteController::class, 'obtenerCliente'])->name('movil.clientes.obtener');
    Route::put('/clientes/{id}', [MovilClienteController::class, 'actualizar'])->name('movil.clientes.actualizar');
    Route::get('/clientes', [MovilClienteController::class, 'listarClientes'])->name('movil.clientes.listar');

    // Rutas de restaurtantes
    Route::get('/restaurantes', [MovilRestauranteController::class, 'listarRestaurantes'])->name('movil.restaurantes.listar');
    Route::get('/restaurante/{id}', [MovilRestauranteController::class, 'obtenerRestaurante'])->name('movil.restaurante.obtener');
});

