@extends('adminlte::page')

@section('title', 'Historial de Pagos y Renovaciones')

@section('content_header')
    <h1>Historial de Pagos y Renovaciones</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title">Pagos Realizados</h3>
    </div>

    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Sección de Pagos --}}
        @if($pagos->isEmpty())
            <div class="alert alert-warning">No hay pagos registrados.</div>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha de Pago</th>
                        <th>Negocio</th>
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
                        <td>{{ $pago->nombre_comercial }}</td>
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
</div>

{{-- Sección de Renovaciones --}}
<div class="card mt-4">
    <div class="card-header bg-info text-white">
        <h3 class="card-title">Renovaciones de Membresías</h3>
    </div>

    <div class="card-body">
        @if($renovaciones->isEmpty())
            <div class="alert alert-warning">No hay renovaciones registradas.</div>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha de Renovación</th>
                        <th>Negocio</th>
                        <th>Plan Renovado</th>
                        <th>Monto</th>
                        <th>Referencia</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($renovaciones as $renovacion)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($renovacion->fecha_pago)->format('d/m/Y H:i') }}</td>
                        <td>{{ $renovacion->nombre_comercial }}</td>
                        <td>{{ ucfirst($renovacion->plan_suscripcion) }}</td>
                        <td>${{ number_format($renovacion->monto_pagado, 2) }}</td>
                        <td>{{ $renovacion->referencia_pago }}</td>
                        <td>
                            <span class="badge {{ $renovacion->estado_pago == 'completado' ? 'bg-success' : ($renovacion->estado_pago == 'pendiente' ? 'bg-warning' : 'bg-danger') }}">
                                {{ ucfirst($renovacion->estado_pago) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
@stop

@section('js')
    <script> console.log('Historial de Pagos y Renovaciones cargado correctamente.'); </script>
@stop
