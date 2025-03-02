<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cliente;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión.');
        }

        // Obtener el cliente asociado
        $usuario = Auth::user();
        $cliente = Cliente::where('id_usuario', $usuario->id_usuario)->first();

        // Si el usuario no tiene un cliente registrado, lo enviamos al perfil
        if (!$cliente) {
            return redirect()->route('perfil')->with('error', 'No se encontró un cliente asociado.');
        }

        // Si la suscripción está vencida, redirigir siempre al perfil
        if (in_array(trim($cliente->estado_suscripcion), ['pendiente', 'cancelado'])) {
            if ($request->route()->getName() !== 'perfil') {
                return redirect()->route('perfil')->with('error', 'Tu suscripción ha expirado. Debes renovarla para acceder al sistema.');
            }
        }

        return $next($request);
    }
}
    