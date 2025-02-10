<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuarios; // Importa el modelo correcto

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Donde redirigir a los usuarios después del login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';  // Redirige al Dashboard

    /**
     * Crear una nueva instancia del controlador.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Método para definir el guard de autenticación.
     *
     * @return mixed
     */
    protected function guard()
    {
        return Auth::guard('web'); // Usa el guard configurado en config/auth.php
    }

    /**
     * Método para redirigir después del login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('dashboard'); // Redirige al dashboard después de loguearse
    }

    /**
     * Método para cerrar sesión.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login'); // Redirige al login después de cerrar sesión
    }
}
    