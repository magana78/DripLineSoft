@extends('adminlte::page')

@section('title', 'Reporte de Ventas')

@section('content_header')
    <h1><i class="fas fa-chart-bar"></i> Reporte de Ventas Mensual</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Reporte de Ventas - {{ date('Y') }}</h3>
            <div class="card-tools">
                <form method="GET" action="{{ route('reportes.ventas') }}">
                    <div class="form-inline">
                        <select name="mes" class="form-control mr-2">
                            @foreach(range(1, 12) as $mes)
                                <option value="{{ $mes }}" 
                                    {{ $mes == $mesSeleccionado ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $mes)->format('F') }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Fecha del Pedido</th>
                        <th>Total</th>
                        <th>Método de Pago</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventasPorMes as $venta)
                        <tr>
                            <td>{{ $venta->fecha_pedido }}</td>
                            <td>${{ number_format($venta->total, 2) }}</td>
                            <td>{{ ucfirst($venta->metodo_pago) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay ventas registradas en este mes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="alert alert-info text-center mt-3">
                <strong>Total de Ventas del Mes:</strong> ${{ number_format($totalVentas, 2) }}
            </div>

            <a href="{{ route('reportes.ventas.pdf', ['mes' => $mesSeleccionado]) }}" 
               class="btn btn-danger btn-block mt-3">
                <i class="fas fa-file-pdf"></i> Descargar PDF
            </a>
        </div>
    </div>
@stop
