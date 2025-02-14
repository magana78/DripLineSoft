<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');  // Redirige a la página de login si no estás autenticado
});

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/registro', [RegisterController::class, 'register']);

// Grupo de rutas protegidas con autenticación
Route::middleware(['auth'])->group(function () {
    
    // Rutas personales antes del resource
    Route::get('/sucursales/inactivas', [SucursalController::class, 'inactivas'])
        ->name('sucursales.inactivas');

    Route::post('/sucursales/{id}/toggle', [SucursalController::class, 'toggleEstado'])->name('sucursales.toggle');

    // Rutas resource index, create, store, show, edit, update, destroy
    Route::resource('sucursales', SucursalController::class);


    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');
});

Auth::routes();
