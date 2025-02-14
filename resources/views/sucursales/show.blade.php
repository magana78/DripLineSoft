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
                    <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#editSucursalModal">
                        <i class="fas fa-edit text-warning"></i> 
                    </button>


                        
                        <form action="{{ route('sucursales.toggle', $sucursal->id_sucursal) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn border-0" data-bs-toggle="tooltip" data-bs-placement="top" title="Desabilitar">
                                <i class="fas {{ $sucursal->activa ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger' }} transition-icon"></i>
                            </button>
                        </form>



                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal de edición -->
<div class="modal fade" id="editSucursalModal" tabindex="-1" aria-labelledby="editSucursalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editSucursalModalLabel">Editar Sucursal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('sucursales.update', $sucursal->id_sucursal) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Nombre de la sucursal -->
                    <div class="mb-3">
                        <label for="nombre_sucursal" class="form-label">Nombre de la Sucursal</label>
                        <input type="text" class="form-control" id="nombre_sucursal" name="nombre_sucursal" value="{{ $sucursal->nombre_sucursal }}" required>
                    </div>

                    <!-- Dirección -->
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" value="{{ $sucursal->direccion }}" required>
                    </div>

                    <!-- Teléfono -->
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" value="{{ $sucursal->telefono }}" required>
                    </div>

                    <!-- Horario -->
                    <div class="row">
                        <div class="col-md-6">
                            <label for="hora_inicio" class="form-label">Hora de Inicio</label>
                            <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" value="{{ old('hora_inicio', explode(' - ', $sucursal->horario_atencion)[0] ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="hora_fin" class="form-label">Hora de Fin</label>
                            <input type="time" class="form-control" id="hora_fin" name="hora_fin" value="{{ old('hora_fin', explode(' - ', $sucursal->horario_atencion)[1] ?? '') }}" required>
                        </div>
                    </div>

                    <!-- Botón de Guardar -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<style>
    .transition-icon {
        transition: color 0.3s ease-in-out;
        font-size: 24px; /* Ajusta el tamaño si lo necesitas */
    }
</style>
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

    document.addEventListener("DOMContentLoaded", function () {
    var modalElement = document.getElementById("editSucursalModal");
    if (modalElement) { // Asegurarse de que el modal existe en el DOM antes de usarlo
        var editModal = new bootstrap.Modal(modalElement);

        var editButton = document.querySelector("[data-bs-target='#editSucursalModal']");
        if (editButton) {
            editButton.addEventListener("click", function () {
                console.log("Clic en Editar");
                editModal.show();
            });
        }
    }
});

</script>

@endsection
