@extends('adminlte::page')

@section('title', 'Reporte de Ventas por Sucursal')

@section('content_header')
    <h1><i class="fas fa-store"></i> Reporte de Ventas por Sucursal</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Reporte de Ventas por Sucursal</h3>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('reportes.sucursales') }}">
                <div class="row">
                    <div class="col-md-4">
                        <label for="sucursal_id">Seleccionar Sucursal:</label>
                        <select name="sucursal_id" class="form-control">
                            <option value="">-- Todas las Sucursales --</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id_sucursal }}">
                                    {{ $sucursal->nombre_sucursal }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="mes">Seleccionar Mes:</label>
                        <select name="mes" class="form-control">
                            <option value="">-- Todos los Meses --</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>

    

            <hr>

            <h4 class="text-center">Total de Ventas: ${{ number_format($totalVentas, 2) }}</h4>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Fecha del Pedido</th>
                        <th>Método de Pago</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedidos as $pedido)
                        <tr>
                            <td>{{ $pedido->id_pedido }}</td>
                            <td>{{ $pedido->fecha_pedido }}</td>
                            <td>{{ ucfirst($pedido->metodo_pago) }}</td>
                            <td>${{ number_format($pedido->total, 2) }}</td>
                        </tr>
                    @endforeach

                    
                </tbody>
            </table>
        </div>
        <form method="POST" action="{{ route('reportes.sucursales.pdf') }}" class="mt-2">
            @csrf
            <input type="hidden" name="sucursal_id" value="{{ request('sucursal_id') }}">
            <input type="hidden" name="mes" value="{{ request('mes') }}">
            <button type="submit" class="btn btn-danger w-100">
                <i class="fas fa-file-pdf"></i> Descargar PDF
            </button>
        </form>
    </div>
@stop
