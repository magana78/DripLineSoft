<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\RegistroController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return redirect('/login');  // Redirige a la página de login si no estás autenticado
});


Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login'); // Redirige al usuario al login después de cerrar sesión
})->name('logout');


// Rutas protegidas por autenticación
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Ruta para procesar el registro del usuario
Route::post('/registro', [RegisterController::class, 'register'])->name('registro');

// Ruta para capturar el pago en PayPal y actualizar la base de datos
Route::post('/paypal/capture-order', [RegisterController::class, 'activateSubscription'])->name('paypal.capture');


Route::post('/paypal/create-order', [PayPalController::class, 'createOrder']);
Route::post('/paypal/capture-order', [PayPalController::class, 'captureOrder']);

Route::get('/dashboard/historial-pagos', [PagoController::class, 'mostrarHistorial'])->name('historial.pagos');


  

Auth::routes();

