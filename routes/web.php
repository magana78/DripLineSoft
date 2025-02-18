<?php


use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\PaymentController;

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

Route::middleware(['auth'])->group(function () {
    Route::get('/sucursales/inactivas', [SucursalController::class, 'inactivas'])->name('sucursales.inactivas');
    Route::post('/sucursales/{id}/toggle', [SucursalController::class, 'toggleEstado'])->name('sucursales.toggle');
    Route::resource('sucursales', SucursalController::class);
    
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');
});

Auth::routes();