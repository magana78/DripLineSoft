<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PerfilController;
use App\Http\Middleware\CheckSubscription;


use App\Http\Controllers\LandingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ReporteSucursalController;
use App\Http\Controllers\Auth\ChangePasswordController;


Route::get('/', function () {
    return redirect('/login');
});

Route::get('/landing', [LandingController::class, 'index'])->name('landing');


Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');





Route::post('/registro', [RegisterController::class, 'register']);

Route::post('/paypal/capture-order', [RegisterController::class, 'activateSubscription'])->name('paypal.capture');
Route::post('/paypal/create-order', [PayPalController::class, 'createOrder']);
Route::post('/paypal/capture-order', [PayPalController::class, 'captureOrder']);


// ✅ Agrupar todas las rutas del "admin_cliente" con middleware de autenticación y rol
Route::middleware(['auth',  'role:admin_cliente', CheckSubscription::class])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/cambiar-contrasena', [ChangePasswordController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/cambiar-contrasena', [ChangePasswordController::class, 'changePassword'])->name('password.change.update');
    


    // Rutas de sucursales
    Route::get('/sucursales/inactivas', [SucursalController::class, 'inactivas'])->name('sucursales.inactivas');
    Route::post('/sucursales/{id}/toggle', [SucursalController::class, 'toggleEstado'])->name('sucursales.toggle');
    Route::resource('sucursales', SucursalController::class);

    // Rutas de pagos
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');


    // Rutas de historial de pagos
    Route::get('/dashboard/historial-pagos', [PagoController::class, 'mostrarHistorial'])->name('historial.pagos');

    // Rutas de pedidos
    Route::get('/dashboard/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/dashboard/pedidos/{id}', [PedidoController::class, 'show'])->name('pedidos.show');

    // ruta pedidos actualizar pedido, cancelar pedido, actualizar tiempo
    Route::put('/pedidos/{id}/update', [PedidoController::class, 'update'])->name('pedidos.update');
    Route::put('/pedidos/{id}/cancel', [PedidoController::class, 'cancel'])->name('pedidos.cancel');
    Route::put('/pedidos/{id}/update-time', [PedidoController::class, 'updateTime'])->name('pedidos.updateTime');

    // Rutas de perfil
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil');
    Route::post('/perfil/update', [PerfilController::class, 'update'])->name('perfil.update');

    Route::delete('/menus/{menu_id}/productos/{producto_id}', [MenuController::class, 'removeProduct'])
    ->name('menus.removeProduct');

    // Rutas de menús
    Route::resource('menus', MenuController::class);

    Route::get('/productos/get-menus/{id_sucursal}', [ProductoController::class, 'getMenusBySucursal'])->name('productos.getMenus');
    Route::post('/productos/{id}/toggle', [ProductoController::class, 'toggleEstado'])->name('productos.toggle');
    Route::resource('productos', ProductoController::class);

    Route::get('/reportes/ventas', [ReporteController::class, 'index'])->name('reportes.ventas');
    Route::get('/reportes/ventas/pdf', [ReporteController::class, 'exportarPDF'])->name('reportes.ventas.pdf');

    Route::get('/reportes/sucursales', [ReporteSucursalController::class, 'index'])->name('reportes.sucursales');
    Route::post('/reportes/sucursales/pdf', [ReporteSucursalController::class, 'exportarPDF'])->name('reportes.sucursales.pdf');

});




Route::post('/perfil/renovar', [PerfilController::class, 'renovar'])->name('perfil.renovar');









Auth::routes();