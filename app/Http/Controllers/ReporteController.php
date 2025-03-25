<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use PDF;
use Carbon\Carbon;

class ReporteController extends Controller
{
    // Mostrar vista del reporte con filtro por mes
    public function index(Request $request)
    {
        $mesSeleccionado = $request->get('mes', Carbon::now()->month);

        $ventasPorMes = Pedido::where('estado', 'entregado')
            ->whereMonth('fecha_pedido', $mesSeleccionado)
            ->whereYear('fecha_pedido', Carbon::now()->year)
            ->get();

        $totalVentas = $ventasPorMes->sum('total');

        return view('reportes.ventas', compact('ventasPorMes', 'mesSeleccionado', 'totalVentas'));
    }

    // Generar PDF con filtro por mes
    public function exportarPDF(Request $request)
    {
        $mesSeleccionado = $request->get('mes', Carbon::now()->month);

        $ventasPorMes = Pedido::where('estado', 'entregado')
            ->whereMonth('fecha_pedido', $mesSeleccionado)
            ->whereYear('fecha_pedido', Carbon::now()->year)
            ->get();

        $totalVentas = $ventasPorMes->sum('total');

        $pdf = PDF::loadView('reportes.ventas_pdf', compact('ventasPorMes', 'mesSeleccionado', 'totalVentas'));

        return $pdf->download('Reporte_Ventas_' . Carbon::now()->format('Y') . '.pdf');
    }
}
