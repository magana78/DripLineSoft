@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg rounded">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Registrar Nueva Sucursal</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('sucursales.store') }}" method="POST" onsubmit="return validarDireccionSeleccionada();">
                        @csrf

                        <!-- Nombre de la sucursal -->
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="nombre_sucursal" name="nombre_sucursal" placeholder="Nombre de la Sucursal" required>
                            <label for="nombre_sucursal">Nombre de la Sucursal</label>
                        </div>

                        <!-- Dirección con autocompletado -->
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Escribe para buscar y selecciona una dirección" required data-seleccionado="false">
                            <ul id="sugerencias" class="list-group mt-1 position-absolute w-100" style="z-index: 1000;"></ul>
                        </div>

                        <!-- Mapa con Leaflet -->
                        <div id="map" style="width: 100%; height: 300px; border-radius: 10px;"></div>

                        <!-- Campos ocultos para latitud y longitud -->
                        <input type="hidden" id="lat" name="lat">
                        <input type="hidden" id="lng" name="lng">

                        <!-- Teléfono -->
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <div class="input-group">
                                <select class="form-select" id="region" name="region">
                                    <option value="+52" selected>México (+52)</option>
                                    <option value="+1">EE.UU (+1)</option>
                                    <option value="+34">España (+34)</option>
                                    <option value="+44">Reino Unido (+44)</option>
                                </select>
                                <input type="tel" class="form-control" id="telefono" name="telefono" maxlength="10" placeholder="Número de teléfono" pattern="[0-9]{10}" required>
                            </div>
                        </div>

                        <!-- Horario -->
                        <div class="row">
                            <div class="col-md-6">
                                <label for="hora_inicio" class="form-label">Hora de Inicio</label>
                                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                            </div>
                            <div class="col-md-6">
                                <label for="hora_fin" class="form-label">Hora de Fin</label>
                                <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
                            </div>
                        </div>

                       <!-- Días de la semana -->
                        <div class="mt-3">
                            <label class="form-label">Días de la semana</label><br>
                            @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="{{ strtolower($dia) }}" name="dias[]" value="{{ $dia }}">
                                    <label class="form-check-label" for="{{ strtolower($dia) }}">{{ $dia }}</label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Botón de Envío -->
                        <button type="submit" class="btn btn-primary mt-3 w-100">Registrar Sucursal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet.js -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    var defaultLat = 19.432608; // Latitud por defecto (CDMX)
    var defaultLng = -99.133209; // Longitud por defecto (CDMX)
    var zoomLevel = 13; // Nivel de zoom

    // Inicializar el mapa con valores por defecto
    var map = L.map('map').setView([defaultLat, defaultLng], zoomLevel);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    function updateLatLng(lat, lng) {
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
    }

    // 🔹 Intentar obtener la ubicación del usuario al cargar la página
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var userLat = position.coords.latitude;
            var userLng = position.coords.longitude;

            // Mover el mapa y marcador a la ubicación del usuario
            map.setView([userLat, userLng], zoomLevel);
            marker.setLatLng([userLat, userLng]);
            updateLatLng(userLat, userLng);

            // Obtener dirección automática de la ubicación
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${userLat}&lon=${userLng}`)
                .then(response => response.json())
                .then(data => {
                    if (data.address && data.address.country_code === 'mx') {
                        document.getElementById('direccion').value = data.display_name;
                        document.getElementById('direccion').setAttribute("data-seleccionado", "true");
                    }
                });
        }, function (error) {
            console.warn('Error en la geolocalización:', error.message);
            alert("No se pudo obtener la ubicación, el mapa usará la ubicación predeterminada.");
        });
    } else {
        alert("Tu navegador no admite geolocalización.");
    }

    // 🔹 Evento: Cuando se mueve el marcador manualmente
    marker.on('dragend', function (e) {
        var latlng = marker.getLatLng();
        updateLatLng(latlng.lat, latlng.lng);

        // Obtener dirección automáticamente cuando se mueve el marcador
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`)
            .then(response => response.json())
            .then(data => {
                if (data.address && data.address.country_code === 'mx') {
                    document.getElementById('direccion').value = data.display_name;
                    document.getElementById('direccion').setAttribute("data-seleccionado", "true");
                } else {
                    alert('Por favor selecciona una ubicación dentro de México.');
                    marker.setLatLng([defaultLat, defaultLng]);
                }
            });
    });

    // 🔹 Autocompletado de direcciones (Solo México)
    document.getElementById('direccion').addEventListener('input', function () {
        var query = this.value;
        if (query.length < 3) return;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=MX&q=${query}`)
            .then(response => response.json())
            .then(data => {
                var sugerencias = document.getElementById('sugerencias');
                sugerencias.innerHTML = '';

                data.forEach(function (item) {
                    var li = document.createElement('li');
                    li.classList.add('list-group-item', 'list-group-item-action');
                    li.textContent = item.display_name;
                    li.setAttribute('data-lat', item.lat);
                    li.setAttribute('data-lon', item.lon);
                    li.addEventListener('click', function () {
                        document.getElementById('direccion').value = item.display_name;
                        document.getElementById('direccion').setAttribute("data-seleccionado", "true");
                        map.setView([item.lat, item.lon], 15);
                        marker.setLatLng([item.lat, item.lon]);
                        updateLatLng(item.lat, item.lon);
                        sugerencias.innerHTML = '';
                    });
                    sugerencias.appendChild(li);
                });
            });
    });

    // 🔹 Cierra las sugerencias si el usuario hace clic fuera del campo de dirección
    document.addEventListener('click', function (event) {
        var sugerencias = document.getElementById('sugerencias');
        if (!document.getElementById('direccion').contains(event.target)) {
            sugerencias.innerHTML = '';
        }
    });

    // 🔹 Validar que el usuario seleccione una dirección de la lista antes de enviar el formulario
    function validarDireccionSeleccionada() {
        var direccion = document.getElementById('direccion').value;
        var seleccionado = document.getElementById('direccion').getAttribute("data-seleccionado");

        if (!direccion || seleccionado !== "true") {
            alert("Debes seleccionar una dirección de la lista.");
            document.getElementById('direccion').value = '';
            return false;
        }
        return true;
    }
</script>

@endsection
