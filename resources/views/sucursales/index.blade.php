@extends('layouts.app')
@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary">Lista de Sucursales</h2>
        
        <!-- Botón flotante para agregar sucursal -->
        <a href="{{ route('sucursales.create') }}" class="btn btn-primary shadow rounded-circle"
           style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-plus"></i>
        </a>
    </div>
    @endsection

@section('content')


    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Definir la función fuera del bucle para evitar redeclaración -->
    @php
        function abreviarDias($dias) {
            $dias = array_values($dias);
            if (count($dias) > 1) {
                return reset($dias) . ' - ' . end($dias);
            }
            return implode(', ', $dias);
        }
    @endphp

    <!-- Verificar si hay sucursales -->
    @if($sucursales->isEmpty())
        <div class="alert alert-warning">No hay sucursales registradas.</div>
    @else
        <div class="row">
            @foreach($sucursales as $sucursal)
                @php
                    // Extraer el horario y los días de la semana
                    preg_match('/^(.*?) \((.*?)\)$/', $sucursal->horario_atencion, $matches);
                    $horario = $matches[1] ?? 'Horario no disponible';
                    $dias = isset($matches[2]) ? explode(', ', $matches[2]) : [];

                    // Generar resumen de días
                    $diasAbreviados = ['Lunes' => 'Lun', 'Martes' => 'Mar', 'Miércoles' => 'Mié', 'Jueves' => 'Jue', 'Viernes' => 'Vie', 'Sábado' => 'Sáb', 'Domingo' => 'Dom'];
                    $diasTrabajados = array_map(fn($dia) => $diasAbreviados[$dia] ?? $dia, $dias);
                    $diasFaltantes = array_diff(array_keys($diasAbreviados), $dias);

                    if (count($diasTrabajados) == 7) {
                        $diasTexto = 'Sin descanso';
                    } elseif (count($diasFaltantes) == 0) {
                        $diasTexto = abreviarDias($diasTrabajados);
                    } else {
                        $diasTexto = abreviarDias($diasTrabajados);
                        if (!empty($diasFaltantes)) {
                            $diasTexto .= '<br><strong>Descanso:</strong> ' . abreviarDias(array_map(fn($dia) => $diasAbreviados[$dia], $diasFaltantes));
                        }
                    }
                @endphp

                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow-lg" style="min-height: 280px; display: flex; flex-direction: column; justify-content: space-between;">
                        <div class="card-body">
                            <h5 class="card-title text-primary">{{ $sucursal->nombre_sucursal }}</h5>
                            <p class="card-text"><strong>Dirección:</strong> {{ $sucursal->direccion }}</p>
                            <p class="card-text"><strong>Teléfono:</strong> {{ $sucursal->telefono }}</p>
                            <p class="card-text"><strong>Horario:</strong> {{ $horario }}</p>
                            <p class="card-text"><strong>Días de la semana:</strong> {!! $diasTexto !!}</p>
                        </div>
                        <div class="card-footer text-center">
                            <a href="{{ route('sucursales.show', $sucursal->id_sucursal) }}" class="btn btn-sm btn-primary">
                                Ver Información
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
<script>
    // Cierra las alertas automáticamente después de 5 segundos
    setTimeout(() => {
        let alertas = document.querySelectorAll('.alert');
        alertas.forEach(alert => {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>