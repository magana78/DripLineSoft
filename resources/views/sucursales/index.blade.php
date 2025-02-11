@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary">Lista de Sucursales</h2>
        
        <!-- Botón flotante para agregar sucursal -->
        <a href="{{ route('sucursales.create') }}" class="btn btn-primary shadow rounded-circle"
           style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-plus"></i>
        </a>
    </div>

    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Verificar si hay sucursales -->
    @if($sucursales->isEmpty())
        <div class="alert alert-warning">No hay sucursales registradas.</div>
    @else
        <div class="row">
            @foreach($sucursales as $sucursal)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-lg">
                        <div class="card-body">
                            <h5 class="card-title text-primary">{{ $sucursal->nombre_sucursal }}</h5>
                            <p class="card-text"><strong>Dirección:</strong> {{ $sucursal->direccion }}</p>
                            <p class="card-text"><strong>Teléfono:</strong> {{ $sucursal->telefono }}</p>
                            <p class="card-text"><strong>Horario:</strong> {{ $sucursal->horario_atencion }}</p>
                            
                            <!-- Botones de acción -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('sucursales.show', $sucursal->id_sucursal) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="{{ route('sucursales.edit', $sucursal->id_sucursal) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <form action="{{ route('sucursales.destroy', $sucursal->id_sucursal) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta sucursal?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
