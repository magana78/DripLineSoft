@extends('adminlte::page')

@section('title', 'Pedidos')

@section('content_header')
    <h1>Lista de Pedidos</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title">Historial de Pedidos</h3>
    </div>

    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($pedidos->isEmpty())
            <div class="alert alert-warning">No hay pedidos registrados.</div>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Método de Pago</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedidos as $pedido)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y H:i') }}</td>
                        <td>{{ ucfirst($pedido->metodo_pago) }}</td>
                        <td>
                            <span class="badge {{ $pedido->estado == 'completado' ? 'bg-success' : ($pedido->estado == 'pendiente' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($pedido->estado) }}
                            </span>
                        </td>
                        <td>${{ number_format($pedido->total, 2) }}</td>
                        <td>
                            <a href="{{ route('pedidos.show', $pedido->id_pedido) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Ver Detalles
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@stop
