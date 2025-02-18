@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Historial de Pagos</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($pagos->isEmpty())
        <p>No hay pagos registrados.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha de Pago</th>
                    <th>Plan</th>
                    <th>Monto</th>
                    <th>Método</th>
                    <th>Referencia</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pagos as $pago)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}</td>
                    <td>{{ ucfirst($pago->plan_suscripcion) }}</td>
                    <td>${{ number_format($pago->monto_pagado, 2) }}</td>
                    <td>{{ ucfirst($pago->metodo_pago) }}</td>
                    <td>{{ $pago->referencia_pago }}</td>
                    <td>
                        <span class="badge {{ $pago->estado_pago == 'completado' ? 'bg-success' : ($pago->estado_pago == 'pendiente' ? 'bg-warning' : 'bg-danger') }}">
                            {{ ucfirst($pago->estado_pago) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
