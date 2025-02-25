<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');  
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

Route::post('/registro', [RegisterController::class, 'register']);

Route::post('/paypal/capture-order', [RegisterController::class, 'activateSubscription'])->name('paypal.capture');
Route::post('/paypal/create-order', [PayPalController::class, 'createOrder']);
Route::post('/paypal/capture-order', [PayPalController::class, 'captureOrder']);

Route::get('/dashboard/historial-pagos', [PagoController::class, 'mostrarHistorial'])->name('historial.pagos');

// ✅ Agrupar todas las rutas del "admin_cliente" con middleware de autenticación y rol
Route::middleware(['auth', 'role:admin_cliente'])->group(function () {
    
    // Rutas de sucursales
    Route::get('/sucursales/inactivas', [SucursalController::class, 'inactivas'])->name('sucursales.inactivas');
    Route::post('/sucursales/{id}/toggle', [SucursalController::class, 'toggleEstado'])->name('sucursales.toggle');
    Route::resource('sucursales', SucursalController::class);

    // Rutas de pagos
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');

    //Productos
    Route::get('/productos/get-menus/{id_sucursal}', [ProductoController::class, 'getMenusBySucursal'])->name('productos.getMenus');
    Route::post('/productos/{id}/toggle', [ProductoController::class, 'toggleEstado'])->name('productos.toggle');


    Route::resource('productos', ProductoController::class);

});

Route::middleware(['auth', 'role:admin_cliente'])->group(function () {
    Route::resource('menus', MenuController::class);
});




Auth::routes();
