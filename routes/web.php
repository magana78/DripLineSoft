<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return redirect('/login');  // Redirige a la página de login si no estás autenticado
});

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.index');  // Redirige a la vista del Dashboard
    });
});

  

Auth::routes();

