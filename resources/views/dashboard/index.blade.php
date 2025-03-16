@extends('adminlte::page')

@section('title', 'Dashboard')



@section('content_header')
<div class="card shadow-sm border-0 bg-light mb-4">
    <div class="card-body text-center">
        <div class="d-flex flex-column align-items-center">
            <h4 class="fw-bold text-primary">
                <i class="fas fa-store text-primary me-2"></i>
                {{ $nombre_negocio }}
            </h4>
            <h6 class="text-muted mt-1">
                <i class="fas fa-user text-secondary me-2"></i>
                {{ $nombre_usuario }}
            </h6>
        </div>
    </div>
</div>

    <h1>Bienvenido al Panel de Administración</h1>
@stop

@section('content')
    <div class="row">
        <!-- Tarjetas de estadísticas -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $usuarios_count }}</h3>
                    <p>Usuarios Registrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $negocios_count }}</h3>
                    <p>Negocios Registrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-store"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $sucursales_count }}</h3>
                    <p>Sucursales Registradas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $pedidos_count }}</h3>
                    <p>Pedidos Registrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Pedidos por Mes</h3>
                </div>
                <div class="card-body">
                    <canvas id="pedidosChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title">Distribución de Pedidos</h3>
                </div>
                <div class="card-body">
                    <canvas id="distribucionChart" width="400" height="300"></canvas>
                    <ul class="list-group mt-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                            Completados <span class="badge badge-success badge-pill">{{ $completados }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                            Pendientes <span class="badge badge-warning badge-pill">{{ $pendientes }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                            Cancelados <span class="badge badge-danger badge-pill">{{ $cancelados }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico de barras: Pedidos por Mes
        var ctxPedidos = document.getElementById('pedidosChart').getContext('2d');
        var pedidosChart = new Chart(ctxPedidos, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_values($meses)) !!},
                datasets: [{
                    label: 'Pedidos Registrados',
                    data: {!! json_encode(array_values($pedidos_por_mes)) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Gráfico de pastel: Distribución de Pedidos
        var ctxDistribucion = document.getElementById('distribucionChart').getContext('2d');
        var distribucionChart = new Chart(ctxDistribucion, {
            type: 'doughnut',
            data: {
                labels: ['Completados', 'Pendientes', 'Cancelados'],
                datasets: [{
                    label: 'Estado de Pedidos',
                    data: [{{ $completados }}, {{ $pendientes }}, {{ $cancelados }}],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false
            }
        });
    </script>
@stop
