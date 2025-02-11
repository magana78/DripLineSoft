<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SucursalController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');  // Redirige a la página de login si no estás autenticado
});

// Rutas protegidas por autenticación
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/registro', [RegisterController::class, 'register']);

// Grupo de rutas protegidas con autenticación
Route::middleware(['auth'])->group(function () {
    Route::resource('sucursales', SucursalController::class);
    Route::post('/sucursales/{id}/toggle', [SucursalController::class, 'toggleEstado'])->name('sucursales.toggle');

});

Auth::routes();
