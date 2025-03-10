<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Providers\AuthServiceProvider; // ✅ Importar AuthServiceProvider
use App\Http\Middleware\CheckUserRole; // ✅ Importar CheckUserRole

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: [
            __DIR__ . '/../routes/api.php',
            __DIR__ . '/../routes/api_v1.php',  
        ],
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        AuthServiceProvider::class, // ✅ Registrar AuthServiceProvider
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => CheckUserRole::class, // ✅ Registrar middleware de roles
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
