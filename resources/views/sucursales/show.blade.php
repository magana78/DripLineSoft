@extends('layouts.app')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3"></div>
@endsection

@section('content')
<div class="container mt-4">
     <!-- Contenedor de Mensajes de Éxito y Error -->
     @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif


    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <a href="{{ route('sucursales.index') }}" class="btn me-2" data-bs-toggle="tooltip" title="Regresar">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h4 class="mb-0">Detalles de la Sucursal</h4>
                </div>
                <div class="card-body">
                    <h3 class="text-primary">Sucursal {{ $sucursal->nombre_sucursal }}</h3>
                    <p><strong>📍 Dirección:</strong> {{ $sucursal->direccion }}</p>
                    <p><strong>📞 Teléfono:</strong> {{ $sucursal->telefono }}</p>
                    <p><strong>🕒 Horario:</strong> {{ $sucursal->horario_atencion }}</p>

                    <!-- Mapa de ubicación de la sucursal -->
                    <div id="map-view" style="width: 100%; height: 300px; border-radius: 10px;"></div>

                    <div class="d-flex justify-content-end mt-3">
                        <!-- Botón de Edición (Flotante y Circular) -->
                        <button type="button" class="btn action-btn edit-btn mr-2" id="openEditModal" data-toggle="tooltip" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>

                        <!-- Botón de Habilitar/Deshabilitar (Flotante) -->
                        <form action="{{ route('sucursales.toggle', $sucursal->id_sucursal) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn action-btn toggle-btn" data-toggle="tooltip" title="Habilitar/Deshabilitar">
                                <i class="fas {{ $sucursal->activa ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger' }}"></i>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@php
    // Extraer código de país y número
    preg_match('/^\+(\d{1,3})(\d{8,10})$/', $sucursal->telefono, $matches);
    $regionSeleccionada = isset($matches[1]) ? "+{$matches[1]}" : "+52"; // Default México
    $numeroSolo = isset($matches[2]) ? $matches[2] : "";
@endphp


<!-- Modal de edición de Sucursal -->
<div class="modal fade" id="editSucursalModal" tabindex="-1" aria-labelledby="editSucursalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-store"></i> Editar Sucursal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('sucursales.update', $sucursal->id_sucursal) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    
                    <!-- Nombre -->
                    <div class="form-group">
                        <label for="nombre_sucursal"><i class="fas fa-tag"></i> Nombre de la Sucursal</label>
                        <input type="text" class="form-control" id="nombre_sucursal" name="nombre_sucursal" value="{{ $sucursal->nombre_sucursal }}" required>
                    </div>

                    <!-- Dirección -->
                    <div class="form-group position-relative">
                        <label for="direccion"><i class="fas fa-map-marker-alt"></i> Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" value="{{ $sucursal->direccion }}" required>
                        <ul id="sugerencias" class="list-group mt-1 position-absolute w-100" style="z-index: 1000;"></ul>
                    </div>

                    <!-- Mapa -->
                    <div id="map-edit" style="width: 100%; height: 300px; border-radius: 10px;"></div>
                    
                    <!-- Latitud y Longitud -->
                    <input type="hidden" id="lat" name="lat" value="{{ $sucursal->latitud }}">
                    <input type="hidden" id="lng" name="lng" value="{{ $sucursal->longitud }}">

                    <!-- Teléfono -->
                    <div class="form-group">
                        <label for="telefono"><i class="fas fa-phone"></i> Teléfono</label>
                        <div class="input-group">
                            <!-- Selector de región -->
                            <select class="form-select" id="region_edit" name="region" required>
                                <option value="+52">México (+52)</option>
                                <option value="+1">EE.UU (+1)</option>
                                <option value="+34">España (+34)</option>
                                <option value="+44">Reino Unido (+44)</option>
                            </select>
                            <!-- Campo de teléfono -->
                            <input type="tel" class="form-control" id="telefono_edit" name="telefono" maxlength="10" placeholder="Número de teléfono" pattern="[0-9]{8,10}" required>
                        </div>
                    </div>


                    <!-- Horario -->
                    @php
                        preg_match('/(\d{2}:\d{2}) - (\d{2}:\d{2}) \((.+)\)/', $sucursal->horario_atencion, $matches);
                        $hora_inicio = $matches[1] ?? '';
                        $hora_fin = $matches[2] ?? '';
                        $diasSeleccionados = explode(', ', $matches[3] ?? '');
                    @endphp
                    <div class="row">
                        <div class="col-md-6">
                            <label for="hora_inicio"><i class="fas fa-clock"></i> Hora de Inicio</label>
                            <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" value="{{ $hora_inicio }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="hora_fin"><i class="fas fa-clock"></i> Hora de Fin</label>
                            <input type="time" class="form-control" id="hora_fin" name="hora_fin" value="{{ $hora_fin }}" required>
                        </div>
                    </div>

                    <!-- Días de la semana -->
                    <div class="mt-3">
                        <label><i class="fas fa-calendar-alt"></i> Días de atención</label><br>
                        @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="{{ strtolower($dia) }}" name="dias[]" value="{{ $dia }}" 
                                {{ in_array($dia, $diasSeleccionados) ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ strtolower($dia) }}">{{ $dia }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Botones del modal -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>




<!-- Leaflet.js -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>



<script>
    let editMap = null;
    let editMarker = null;
    
    // 📍 Datos originales de la sucursal para resetear
    const originalData = {
        nombre_sucursal: "{{ $sucursal->nombre_sucursal }}",
        direccion: "{{ $sucursal->direccion }}",
        lat: "{{ $sucursal->latitud }}",
        lng: "{{ $sucursal->longitud }}",
        telefono: "{{ $sucursal->telefono }}",
        hora_inicio: "{{ $hora_inicio }}",
        hora_fin: "{{ $hora_fin }}"
    };

    // 🗺️ Mapa de solo visualización
    document.addEventListener("DOMContentLoaded", function () {
        var viewMap = L.map('map-view').setView([originalData.lat, originalData.lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(viewMap);
        L.marker([originalData.lat, originalData.lng]).addTo(viewMap)
            .bindPopup(originalData.nombre_sucursal)
            .openPopup();
    });

    // 🗺️ Inicializa el mapa en el modal
    function initEditMap() {
        let mapContainer = document.getElementById('map-edit');

        if (!mapContainer) {
            console.error("No se encontró el contenedor del mapa.");
            return;
        }

        setTimeout(() => {
            // Eliminar el mapa si ya existe
            if (editMap) {
                editMap.remove();
            }

            // Verifica que latitud y longitud sean válidas antes de inicializar el mapa
            if (!originalData.lat || !originalData.lng) {
                console.error("Latitud o longitud no definidas.");
                return;
            }

            // Inicializar el mapa con la ubicación original
            editMap = L.map(mapContainer).setView([originalData.lat, originalData.lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(editMap);

            editMarker = L.marker([originalData.lat, originalData.lng], { draggable: true }).addTo(editMap);

            // 📌 Actualizar la dirección cuando el usuario mueva el marcador
            editMarker.on('dragend', function () {
                let latlng = editMarker.getLatLng();
                document.getElementById('lat').value = latlng.lat;
                document.getElementById('lng').value = latlng.lng;
                obtenerDireccion(latlng.lat, latlng.lng);
            });

            // Forzar actualización del tamaño del mapa después de abrir el modal
            setTimeout(() => {
                if (editMap) {
                    editMap.invalidateSize();
                }
            }, 500);
        }, 500);
    }


    // 📌 Abre el modal y carga los datos originales
    document.getElementById("openEditModal").addEventListener("click", function () {
        var editModal = new bootstrap.Modal(document.getElementById("editSucursalModal"));
        editModal.show();

        setTimeout(initEditMap, 500);
    });

    // ❌ Cierra el modal y resetea los datos al estado original
    document.addEventListener("DOMContentLoaded", function () {
        let modal = document.getElementById("editSucursalModal");
        let closeButtons = modal.querySelectorAll(".close, .btn-secondary");

        closeButtons.forEach(button => {
            button.addEventListener("click", function () {
                document.activeElement.blur(); // Elimina el foco antes de cerrar el modal
                
                // Restablecer los valores originales
                document.getElementById('nombre_sucursal').value = originalData.nombre_sucursal;
                document.getElementById('direccion').value = originalData.direccion;
                document.getElementById('lat').value = originalData.lat;
                document.getElementById('lng').value = originalData.lng;
                document.getElementById('telefono_edit').value = originalData.telefono;
                document.getElementById('hora_inicio').value = originalData.hora_inicio;
                document.getElementById('hora_fin').value = originalData.hora_fin;
                document.getElementById('sugerencias').innerHTML = ""; // Limpiar sugerencias
                
                setTimeout(() => {
                    if (editMap) {
                        editMap.remove();
                        editMap = null;
                    }
                }, 300);
            });
        });
    });

    // 📌 Función para obtener dirección a partir de latitud y longitud
    function obtenerDireccion(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                if (data.display_name) {
                    document.getElementById('direccion').value = data.display_name;
                } else {
                    document.getElementById('direccion').value = "Dirección no encontrada";
                }
            })
            .catch(error => console.error("Error obteniendo la dirección:", error));
    }

    // 🔍 Autocompletado de direcciones con sugerencias
    let timeoutID = null;

    document.getElementById('direccion').addEventListener('input', function () {
        let query = this.value.trim();

        if (query.length < 3) {
            document.getElementById('sugerencias').innerHTML = "";
            return;
        }

        if (timeoutID) {
            clearTimeout(timeoutID);
        }

        timeoutID = setTimeout(() => {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=MX&q=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) throw new Error("Error en la solicitud a OpenStreetMap");
                    return response.json();
                })
                .then(data => {
                    let sugerencias = document.getElementById('sugerencias');
                    sugerencias.innerHTML = "";

                    data.forEach(item => {
                        let li = document.createElement('li');
                        li.classList.add('list-group-item', 'list-group-item-action');
                        li.textContent = item.display_name;
                        li.setAttribute('data-lat', item.lat);
                        li.setAttribute('data-lon', item.lon);

                        li.addEventListener('click', function () {
                            document.getElementById('direccion').value = item.display_name;
                            document.getElementById('lat').value = item.lat;
                            document.getElementById('lng').value = item.lon;

                            if (editMap) {
                                editMap.setView([item.lat, item.lon], 15);
                                editMarker.setLatLng([item.lat, item.lon]);
                            }

                            sugerencias.innerHTML = "";
                        });

                        sugerencias.appendChild(li);
                    });
                })
                .catch(error => console.error("Error en la búsqueda de direcciones:", error));
        }, 500);
    });

    // Ocultar lista de sugerencias al hacer clic fuera
    document.addEventListener("click", function (event) {
        let sugerencias = document.getElementById("sugerencias");
        let direccionInput = document.getElementById("direccion");

        if (!direccionInput.contains(event.target)) {
            sugerencias.innerHTML = "";
        }
    });

</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Establecer valores en los campos cuando se abre el modal
        document.getElementById('region_edit').value = "{{ $regionSeleccionada }}";
        document.getElementById('telefono_edit').value = "{{ $numeroSolo }}";
    });

    // Validar que solo se ingresen números y máximo 10 caracteres en el teléfono
    document.getElementById('telefono_edit').addEventListener('input', function (e) {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });

    
</script>



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




@endsection
