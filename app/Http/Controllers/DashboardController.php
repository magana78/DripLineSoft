<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Models\Sucursale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $usuarios_count = Usuario::count();
        $negocios_count = Cliente::count();
        $sucursales_count = Sucursale::count();
        $pedidos_count = Pedido::count();

        // Contadores de pedidos por estado
        $completados = Pedido::where('estado', 'listo')->count();
        $pendientes = Pedido::where('estado', 'pendiente')->count();
        $cancelados = Pedido::where('estado', 'cancelado')->count();

        // Obtener pedidos por mes
        $pedidos_raw = Pedido::select(
            DB::raw("MONTH(fecha_pedido) as mes"),
            DB::raw("COUNT(*) as total")
        )->groupBy('mes')
        ->orderBy('mes', 'asc')
        ->get();

        $pedidos_por_mes = [];
        foreach ($pedidos_raw as $pedido) {
            $pedidos_por_mes[intval($pedido->mes)] = $pedido->total;
        }

        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 
            6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 
            10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        foreach (range(1, 12) as $mes) {
            if (!isset($pedidos_por_mes[$mes])) {
                $pedidos_por_mes[$mes] = 0;
            }
        }

        ksort($pedidos_por_mes);

        // 🔥 Obtener el cliente asociado al usuario autenticado
        $usuario = Auth::user();
        $cliente = Cliente::where('id_usuario', $usuario->id_usuario)->first();

        $nombre_negocio = $cliente->nombre_comercial ?? 'Negocio Desconocido';
        $nombre_usuario = $usuario->nombre ?? 'Usuario Desconocido';
        $logo = $cliente->logo ?? null; // Obtener el logo del negocio desde la BD

        return view('dashboard.index', compact(
            'usuarios_count', 'negocios_count', 'sucursales_count', 'pedidos_count',
            'completados', 'pendientes', 'cancelados', 'pedidos_por_mes', 'meses',
            'nombre_negocio', 'nombre_usuario', 'logo' // ✅ Enviar el logo correctamente
        ));
    }
}
