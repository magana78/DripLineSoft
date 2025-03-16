<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Models\Sucursale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // 🔥 Importar la clase Auth para obtener el usuario autenticado

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

        // Transformar datos a formato JSON para Chart.js
        $pedidos_por_mes = [];
        foreach ($pedidos_raw as $pedido) {
            $pedidos_por_mes[intval($pedido->mes)] = $pedido->total;
        }

        // Nombres de los meses en español
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 
            6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 
            10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        // Asegurar que todos los meses están en el array con valor 0 si no hay datos
        foreach (range(1, 12) as $mes) {
            if (!isset($pedidos_por_mes[$mes])) {
                $pedidos_por_mes[$mes] = 0;
            }
        }

        // Ordenar correctamente los meses
        ksort($pedidos_por_mes);

        // 🔥 Agregar el nombre del negocio y del usuario autenticado
        $usuario = Auth::user();
        $nombre_negocio = $usuario->cliente->nombre_comercial ?? 'Negocio Desconocido';
        $nombre_usuario = $usuario->nombre ?? 'Usuario Desconocido';

        return view('dashboard.index', compact(
            'usuarios_count', 'negocios_count', 'sucursales_count', 'pedidos_count',
            'completados', 'pendientes', 'cancelados', 'pedidos_por_mes', 'meses',
            'nombre_negocio', 'nombre_usuario' // 🔥 Se envían estos datos a la vista
        ));
    }
}
