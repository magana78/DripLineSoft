<?php

use App\Http\Controllers\Auth\RegisterController;
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

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/registro', [RegisterController::class, 'register']);

Route::post('/paypal/pay', [PayPalController::class, 'createPayment'])->name('paypal.pay');
Route::post('/paypal/capture', [PayPalController::class, 'capturePayment'])->name('paypal.capture');


  

Auth::routes();

