<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Sucursale;
use PDF;
use Carbon\Carbon;

class ReporteSucursalController extends Controller
{
    // Mostrar vista del reporte por sucursal
    public function index(Request $request)
    {
        $sucursales = Sucursale::all();

        // Filtrar por sucursal y mes
        $query = Pedido::where('estado', 'entregado');

        if ($request->filled('sucursal_id')) {
            $query->where('id_sucursal', $request->sucursal_id);
        }

        if ($request->filled('mes')) {
            $query->whereMonth('fecha_pedido', $request->mes)
                  ->whereYear('fecha_pedido', Carbon::now()->year);
        }

        $pedidos = $query->get();
        $totalVentas = $pedidos->sum('total');

        return view('reportes.sucursales', compact('pedidos', 'totalVentas', 'sucursales'));
    }

    // Generar el PDF del reporte de ventas por sucursal
    public function exportarPDF(Request $request)
    {
        $query = Pedido::where('estado', 'entregado');

        if ($request->filled('sucursal_id')) {
            $query->where('id_sucursal', $request->sucursal_id);
        }

        if ($request->filled('mes')) {
            $query->whereMonth('fecha_pedido', $request->mes)
                  ->whereYear('fecha_pedido', Carbon::now()->year);
        }

        $pedidos = $query->get();
        $totalVentas = $pedidos->sum('total');

        $pdf = PDF::loadView('reportes.sucursales_pdf', compact('pedidos', 'totalVentas'));

        return $pdf->download('Reporte_Ventas_Sucursal_' . Carbon::now()->format('Y_m') . '.pdf');
    }
}
