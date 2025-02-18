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
    }
}
