@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <!-- Botón de regresar -->
                        <a href="{{ route('sucursales.index') }}" class="btn me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Regresar">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    <h4 class="mb-0">Detalles de la Sucursal</h4>
                </div>
                <div class="card-body">
                    <h3 class="text-primary">Sucursal {{ $sucursal->nombre_sucursal }}</h3>
                    
                    <p><strong>📍 Dirección:</strong> {{ $sucursal->direccion }}</p>
                    <p><strong>📞 Teléfono:</strong> {{ $sucursal->telefono }}</p>
                    <p><strong>🕒 Horario:</strong> {{ $sucursal->horario_atencion }}</p>

                    <!-- Mapa de ubicación -->
                    <div id="map" style="width: 100%; height: 300px; border-radius: 10px;"></div>

                    <div class="d-flex justify-content-end mt-3">
                        <!-- Botón de editar -->
                        <a href="{{ route('sucursales.edit', $sucursal->id_sucursal) }}" class="btn me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>

                        <!-- Botón de eliminar -->
                        <form action="{{ route('sucursales.destroy', $sucursal->id_sucursal) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta sucursal?');">
                            @csrf
                            <button type="submit" class="btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Eliminar">
                                <i class="fas fa-trash text-danger"></i>
                            </button>
                        </form>

                        <form action="{{ route('sucursales.toggle', $sucursal->id_sucursal) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn {{ $sucursal->activa ? 'btn-danger' : 'btn-success' }}">
                                <i class="fas {{ $sucursal->activa ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet.js para el mapa -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    var lat = {{ $sucursal->latitud }};
    var lng = {{ $sucursal->longitud }};
    var map = L.map('map').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup("{{ $sucursal->nombre_sucursal }}")
        .openPopup();
</script>

<!-- Script para activar los tooltips de Bootstrap -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

@endsection
