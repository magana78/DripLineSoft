<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PagoController extends Controller
{
    /**
     * Mostrar el historial de pagos del usuario autenticado.
     */
    public function mostrarHistorial()
    {
        // Obtener el cliente autenticado basado en su ID de usuario
        $cliente = Cliente::where('id_usuario', Auth::id())->first();

        if (!$cliente) {
            return redirect()->back()->with('error', 'No se encontró información de pagos.');
        }

        // Obtener todos los pagos del cliente autenticado
        $pagos = DB::table('pagos_suscripcion')
            ->where('id_cliente', $cliente->id_cliente)
            ->orderBy('fecha_pago', 'desc')
            ->get();

        return view('dashboard.historial', compact('pagos')); // 🔹 Ajustamos la ruta de la vista
    }
}
