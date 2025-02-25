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
                        <h4 class="mb-0">Información del Producto</h4>
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
                            <div class="alert alert-warning">No tienes sucursales disponibles. Debes registrar al menos una
                                antes de agregar productos.</div>
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
                                    <div id="menu-alert" class="alert alert-warning mt-2 d-none">Esta sucursal no tiene menús
                                        registrados.</div>
                                </div>

                                <!-- Nombre del Producto -->
                                <div class="form-floating mb-3">
                                    <label for="nombre_producto">Nombre del Producto</label>

                                    <input type="text" class="form-control" id="nombre_producto" name="nombre_producto"
                                        placeholder="Nombre del Producto" required>
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
                                <div class="mb-3">
                                    <label class="form-label">Imágenes del Producto (Máx. 4)</label>
                                    <input type="file" id="imagenes" class="form-control transition-effect" accept="image/*">
                                    <small id="file-limit-warning" class="text-danger d-none">⚠️ Has alcanzado el límite de 4
                                        imágenes.</small>
                                    <ul class="list-group mt-3" id="file-list"></ul>

                                    <input type="file" id="imagenes_hidden" name="imagenes[]" multiple class="d-none">
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
        document.getElementById('id_sucursal')?.addEventListener('change', function () {
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

        let selectedFiles = new DataTransfer();

        document.getElementById('imagenes').addEventListener('change', function (event) {
            let previewContainer = document.getElementById('preview-container');
            let file = event.target.files[0];

            if (!file) return; // Si no se seleccionó nada, salir

            if (selectedFiles.files.length >= 4) {
                alert('Solo puedes agregar un máximo de 4 imágenes.');
                return;
            }

            selectedFiles.items.add(file);
            updateImagePreview();
            updateFileInput();
            toggleFileInputVisibility();

            event.target.value = ""; // Resetear el input para permitir nuevas selecciones
        });

        function updateImagePreview() {
            let fileList = document.getElementById('file-list');
            fileList.innerHTML = ""; // Limpia la lista anterior

            Array.from(selectedFiles.files).forEach((file, index) => {
                let reader = new FileReader();
                reader.onload = function (e) {
                    let listItem = document.createElement('li');
                    listItem.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');

                    let img = document.createElement('img');
                    img.src = e.target.result;
                    img.classList.add('img-thumbnail', 'mr-2');
                    img.style.width = '50px';
                    img.style.height = '50px';
                    img.style.objectFit = 'cover';

                    let fileInfo = document.createElement('span');
                    fileInfo.innerHTML = `<strong>${file.name}</strong> <small class="text-muted">(${(file.size / 1024).toFixed(2)} KB)</small>`;

                    let actions = document.createElement('div');

                    // Botón de ver en pantalla completa con URL temporal
                    let viewButton = document.createElement('button');
                    viewButton.classList.add('btn', 'btn-sm', 'btn-outline-primary', 'mr-1');
                    viewButton.innerHTML = '<i class="fas fa-expand"></i>';
                    viewButton.onclick = function () {
                        let blob = new Blob([file], { type: file.type }); // Crear Blob
                        let blobUrl = URL.createObjectURL(blob); // Generar URL temporal
                        window.open(blobUrl, '_blank'); // Abrir en una nueva pestaña

                        // Liberar memoria después de abrir
                        setTimeout(() => {
                            URL.revokeObjectURL(blobUrl);
                        }, 5000); // La URL se revoca después de 5 segundos
                    };


                    // Botón de descargar (Solo en local no funciona, pero en servidor sí)
                    let downloadButton = document.createElement('a');
                    downloadButton.href = e.target.result;
                    downloadButton.download = file.name;
                    downloadButton.classList.add('btn', 'btn-sm', 'btn-outline-success', 'mr-1');
                    downloadButton.innerHTML = '<i class="fas fa-download"></i>';

                    // Botón de eliminar
                    let deleteButton = document.createElement('button');
                    deleteButton.classList.add('btn', 'btn-sm', 'btn-outline-danger');
                    deleteButton.innerHTML = '<i class="fas fa-times"></i>';
                    deleteButton.onclick = function () {
                        selectedFiles.items.remove(index);
                        updateImagePreview();
                        updateFileInput();
                        toggleFileInputVisibility();
                    };

                    actions.appendChild(viewButton);
                    actions.appendChild(downloadButton);
                    actions.appendChild(deleteButton);

                    listItem.appendChild(img);
                    listItem.appendChild(fileInfo);
                    listItem.appendChild(actions);

                    fileList.appendChild(listItem);
                };
                reader.readAsDataURL(file);
            });
        }


        // **Actualiza el input hidden con las imágenes seleccionadas**
        function updateFileInput() {
            document.getElementById('imagenes_hidden').files = selectedFiles.files;
        }

        // **Ocultar o mostrar el input de selección de archivos**
        function toggleFileInputVisibility() {
            let fileInput = document.getElementById('imagenes');
            let fileLimitWarning = document.getElementById('file-limit-warning');

            if (selectedFiles.files.length >= 4) {
                fileInput.classList.add('hidden'); // Aplica la animación de ocultar
                setTimeout(() => {
                    fileInput.classList.add('d-none'); // Oculta completamente después de la animación
                }, 300);
                fileLimitWarning.classList.remove('d-none'); // Muestra el mensaje de advertencia
            } else {
                fileLimitWarning.classList.add('d-none'); // Oculta el mensaje si hay menos de 4 imágenes
                fileInput.classList.remove('d-none'); // Muestra el input de nuevo
                setTimeout(() => {
                    fileInput.classList.remove('hidden'); // Aplica la animación de mostrar
                }, 10);
            }
        }





    </script>
@endsection
