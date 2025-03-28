<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Usuario;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerPolicies();

        // Definir permisos (Gates) según el rol
        Gate::define('manage-sucursales', function (Usuario $user) {
            return $user->rol === 'admin_cliente'; // Solo admin_cliente puede gestionar sucursales
        });

        Gate::define('view-payments', function (Usuario $user) {
            return $user->rol === 'admin_cliente'; // Solo admin_cliente puede ver pagos
        });

        Gate::define('access-dashboard', function (Usuario $user) {
            return in_array($user->rol, ['admin_sistema', 'admin_cliente']); // Todos los roles pueden acceder
        });

        Gate::define('manage-menus', function (Usuario $user) {
            return $user->rol === 'admin_cliente'; // Solo admin_cliente puede ver menús
        });
        Gate::define('manage-products', function (Usuario $user) {
            return $user->rol === 'admin_cliente'; // Solo admin_cliente puede ver menús
        });

        Gate::define('manage-pedidos', function (Usuario $user) {
            return $user->rol === 'admin_cliente'; // Solo admin_cliente puede ver menús
        });
        
        Gate::define('auth', function (Usuario $user) {
            return $user->rol === 'admin_cliente'; // Solo admin_cliente puede ver menús
        });

        Gate::define('auth-reportes', function (Usuario $user) {
            return $user->rol === 'admin_cliente'; // Solo admin_cliente puede ver menús
        });

        Gate::define('aut-sucursales', function (Usuario $user) {
            return $user->rol === 'admin_cliente'; // Solo admin_cliente puede ver menús
        });
        Gate::define('access-payments-history', function (Usuario $user) {
            return $user->rol === 'admin_cliente'; // Solo admin_cliente puede ver menús
        });
    }
}
