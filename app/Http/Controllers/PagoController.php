<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PagoController extends Controller
{
    /**
     * Mostrar el historial de pagos del usuario autenticado y su negocio.
     */
    public function mostrarHistorial()
    {
        // Obtener el cliente autenticado basado en su ID de usuario
        $cliente = Cliente::where('id_usuario', Auth::id())->first();

        if (!$cliente) {
            return redirect()->back()->with('error', 'No se encontró información de pagos.');
        }

        // Obtener todos los pagos del cliente autenticado y su negocio
        $pagos = DB::table('pagos_suscripcion')
            ->join('clientes', 'pagos_suscripcion.id_cliente', '=', 'clientes.id_cliente')
            ->select(
                'pagos_suscripcion.*', 
                'clientes.nombre_comercial'
            )
            ->where('clientes.id_cliente', $cliente->id_cliente)
            ->orderBy('fecha_pago', 'desc')
            ->get();

        return view('dashboard.historial', compact('pagos'));
    }
}