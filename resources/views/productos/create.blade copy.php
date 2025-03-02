@extends('layouts.app')
@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary">Registrar Nuevo Producto</h2>
    </div>
@endsection

@section('content')
<div class="container mt-1">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg rounded">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Registrar Nuevo Producto</h4>
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

                    @if ($sucursales->isEmpty())
                        <div class="alert alert-warning">No tienes sucursales disponibles. Debes registrar al menos una antes de agregar productos.</div>
                    @else
                        <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- Selección de Sucursal -->
                            <div class="form-group">
                                <label for="id_sucursal">Sucursal</label>
                                <select id="id_sucursal" name="id_sucursal" class="form-control">
                                    <option value="">Seleccione una sucursal</option>
                                    @foreach ($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id_sucursal }}">{{ $sucursal->nombre_sucursal }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Selección de Menú -->
                            <div class="form-group d-none" id="menu-container">
                                <label for="id_menu">Menú</label>
                                <div class="position-relative">
                                    <div class="input-group">
                                        <select id="id_menu" name="id_menu" class="form-control d-none" disabled>
                                            <option value="">Seleccione un menú</option>
                                        </select>
                                        <div id="menu-spinner" class="spinner-border text-primary d-none" role="status">
                                            <span class="sr-only">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                                <div id="menu-alert" class="alert alert-warning mt-2 d-none">Esta sucursal no tiene menús registrados.</div>
                            </div>

                            <!-- Nombre del Producto -->
                            <div class="form-floating mb-3">
                                <label for="nombre_producto">Nombre del Producto</label>

                                <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" placeholder="Nombre del Producto" required>
                            </div>

                            <!-- Descripción -->
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea id="descripcion" name="descripcion" class="form-control"></textarea>
                            </div>

                            <!-- Precio -->
                            <div class="mb-3">
                                <label for="precio" class="form-label">Precio</label>
                                <input type="number" step="0.01" id="precio" name="precio" class="form-control" required>
                            </div>

                            <!-- Disponibilidad -->
                            <div class="mb-3">
                                <label for="disponible" class="form-label">Disponibilidad</label>
                                <select id="disponible" name="disponible" class="form-control">
                                    <option value="1">Disponible</option>
                                    <option value="0">No Disponible</option>
                                </select>
                            </div>

                           <!-- Imágenes del Producto -->
                            <!-- Imágenes del Producto -->
                            <div class="mb-3">
                                <label for="imagenes" class="form-label">Imágenes del Producto (Máx. 4)</label>
                                <input type="file" id="imagenes" class="form-control" multiple accept="image/*">
                                <div class="mt-3 d-flex flex-wrap" id="preview-container"></div>
                            </div>


                            <!-- Botón de Envío -->
                            <button type="submit" class="btn btn-primary mt-3 w-100">Registrar Producto</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('id_sucursal')?.addEventListener('change', function() {
        let menuContainer = document.getElementById('menu-container');
        let menuSelect = document.getElementById('id_menu');
        let menuAlert = document.getElementById('menu-alert');
        let menuSpinner = document.getElementById('menu-spinner');

        menuSelect.innerHTML = '<option value="">Seleccione un menú</option>';
        menuSelect.classList.add('d-none');
        menuSelect.disabled = true;
        menuContainer.classList.remove('d-none');
        menuAlert.classList.add('d-none');
        menuSpinner.classList.remove('d-none');

        if (!this.value) {
            menuContainer.classList.add('d-none');
            return;
        }

        fetch(`/productos/get-menus/${this.value}`)
            .then(response => response.json())
            .then(data => {
                menuSpinner.classList.add('d-none');
                if (data.length > 0) {
                    menuSelect.classList.remove('d-none');
                    menuSelect.disabled = false;
                    data.forEach(menu => {
                        menuSelect.innerHTML += `<option value="${menu.id_menu}">${menu.nombre_menu}</option>`;
                    });
                } else {
                    menuAlert.classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error al obtener los menús:', error);
                alert('Hubo un error al cargar los menús. Inténtelo de nuevo.');
                menuSpinner.classList.add('d-none');
            });
    });

    let selectedFiles = [];

document.getElementById('imagenes').addEventListener('change', function(event) {
    let previewContainer = document.getElementById('preview-container');
    let newFiles = Array.from(event.target.files);

    // Verifica si ya hay 4 imágenes, evita agregar más
    if (selectedFiles.length + newFiles.length > 4) {
        alert('Solo puedes agregar un máximo de 4 imágenes.');
        return;
    }

    newFiles.forEach(file => {
        selectedFiles.push(file);
        let reader = new FileReader();
        reader.onload = function(e) {
            let imageContainer = document.createElement('div');
            imageContainer.classList.add('position-relative', 'm-2');
            imageContainer.style.width = '100px';
            imageContainer.style.height = '100px';

            let img = document.createElement('img');
            img.src = e.target.result;
            img.classList.add('img-thumbnail');
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            img.onclick = function() { window.open(img.src, '_blank'); };

            let closeButton = document.createElement('button');
            closeButton.classList.add('btn', 'btn-sm', 'position-absolute', 'top-0', 'end-0', 'text-white', 'bg-dark', 'border-0', 'rounded-circle');
            closeButton.innerHTML = '&times;';
            closeButton.onclick = function() {
                selectedFiles = selectedFiles.filter(f => f !== file);
                imageContainer.remove();
                updateFileInput();
            };

            imageContainer.appendChild(img);
            imageContainer.appendChild(closeButton);
            previewContainer.appendChild(imageContainer);
        };
        reader.readAsDataURL(file);
    });

    updateFileInput();
    event.target.value = ""; // Resetea el input para permitir nuevas selecciones sin mostrar archivos previos
});

function updateFileInput() {
    let dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    document.getElementById('imagenes').files = dataTransfer.files;
}

</script>
@endsection
