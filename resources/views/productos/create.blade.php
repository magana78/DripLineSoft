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
                        <div class="form-group">
                            <label class="form-label">Imágenes del Producto (Máx. 4)</label>
                            <ul class="list-group mt-3" id="file-list-edit">
                                @foreach($producto->imagenes_productos as $imagen)
                                <li class="list-group-item d-flex justify-content-between align-items-center image-item" data-id="{{ $imagen->id_imagen }}">
                                    <img src="{{ asset('storage/' . $imagen->ruta_imagen) }}" class="img-thumbnail mr-2" style="width: 50px; height: 50px; object-fit: cover;">
                                    <span><strong>Imagen actual</strong></span>
                                    <div>
                                        <input type="file" class="d-none image-input" name="new_images[]" accept="image/*">
                                        <button type="button" class="btn btn-sm btn-outline-warning edit-image-btn"><i class="fas fa-edit"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-image-btn" data-id="{{ $imagen->id_imagen }}"><i class="fas fa-times"></i></button>
                                    </div>
                                </li>
                                @endforeach
                            </ul>

                            <!-- Campo oculto para imágenes actuales -->
                            @foreach($producto->imagenes_productos as $imagen)
                            <input type="hidden" name="existing_images[]" value="{{ $imagen->id_imagen }}">
                            @endforeach

                            <input type="file" id="imagenes_edit" name="imagenes[]" class="form-control mt-2" accept="image/*" multiple>
                            <small id="file-limit-warning-edit" class="text-danger d-none">⚠️ Máximo de 4 imágenes alcanzado.</small>

                            <!-- Campo oculto para imágenes eliminadas -->
                            <input type="hidden" id="deleted_images" name="deleted_images" value="">
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
    document.addEventListener("DOMContentLoaded", function () {
        let deletedImages = [];

        $('#editProductModal').on('shown.bs.modal', function () {
            console.log("Modal de edición abierto");

            // Precargar datos del producto
            $('#nombre_producto_edit').val("{{ $producto->nombre_producto }}");
            $('#descripcion_edit').val("{{ $producto->descripcion }}");
            $('#precio_edit').val("{{ $producto->precio }}");
            $('#id_sucursal_edit').val("{{ $producto->menu->sucursale->id_sucursal }}");

            // Precargar menú si el producto ya tiene uno asignado
            cargarMenus("{{ $producto->menu->sucursale->id_sucursal }}", "{{ $producto->menu->id_menu }}");
            verificarCantidadImagenes();
        });

        // Evento para cambiar la sucursal y cargar menús dinámicamente
        document.getElementById('id_sucursal_edit')?.addEventListener('change', function () {
            let sucursalId = this.value;
            cargarMenus(sucursalId);
        });

        function cargarMenus(sucursalId, selectedMenu = null) {
            let menuContainer = document.getElementById('menu-container-edit');
            let menuSelect = document.getElementById('id_menu_edit');
            let menuAlert = document.getElementById('menu-alert-edit');
            let menuSpinner = document.getElementById('menu-spinner-edit');

            menuSelect.innerHTML = '<option value="">Seleccione un menú</option>';
            menuSelect.classList.add('d-none');
            menuSelect.disabled = true;
            menuContainer.classList.remove('d-none');
            menuAlert.classList.add('d-none');
            menuSpinner.classList.remove('d-none');

            if (!sucursalId) {
                menuContainer.classList.add('d-none');
                return;
            }

            fetch(`/productos/get-menus/${sucursalId}`)
                .then(response => response.json())
                .then(data => {
                    menuSpinner.classList.add('d-none');
                    if (data.length > 0) {
                        menuSelect.classList.remove('d-none');
                        menuSelect.disabled = false;
                        data.forEach(menu => {
                            let selected = selectedMenu && selectedMenu == menu.id_menu ? "selected" : "";
                            menuSelect.innerHTML += `<option value="${menu.id_menu}" ${selected}>${menu.nombre_menu}</option>`;
                        });
                    } else {
                        menuAlert.removeClass('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error al obtener los menús:', error);
                    alert('Hubo un error al cargar los menús. Inténtelo de nuevo.');
                    menuSpinner.classList.add('d-none');
                });
        }

        // Editar imagen existente
        document.querySelectorAll('.edit-image-btn').forEach(button => {
            button.addEventListener('click', function () {
                let inputFile = this.previousElementSibling;
                inputFile.click();

                inputFile.addEventListener('change', function (event) {
                    let file = event.target.files[0];
                    if (file) {
                        let reader = new FileReader();
                        reader.onload = function (e) {
                            let img = inputFile.closest('li').querySelector('img');
                            img.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        });

        // Eliminar imagen
        document.querySelectorAll('.remove-image-btn').forEach(button => {
            button.addEventListener('click', function () {
                let listItem = this.closest('li');
                let imageId = this.dataset.id;

                if (imageId) {
                    deletedImages.push(imageId);
                    document.getElementById('deleted_images').value = deletedImages.join(",");
                }

                listItem.remove();
                verificarCantidadImagenes();
            });
        });

        // Evento para agregar nuevas imágenes
        document.getElementById('imagenes_edit').addEventListener('change', function (event) {
            let fileList = document.getElementById('file-list-edit');
            let maxFiles = 4;

            Array.from(event.target.files).forEach(file => {
                if (fileList.childElementCount >= maxFiles) {
                    document.getElementById('file-limit-warning-edit').classList.remove('d-none');
                    return;
                }

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
                    fileInfo.innerHTML = `<strong>Nueva imagen</strong>`;

                    let actions = document.createElement('div');

                    let deleteButton = document.createElement('button');
                    deleteButton.classList.add('btn', 'btn-sm', 'btn-outline-danger');
                    deleteButton.innerHTML = '<i class="fas fa-times"></i>';
                    deleteButton.onclick = function () {
                        listItem.remove();
                        verificarCantidadImagenes();
                    };

                    actions.appendChild(deleteButton);
                    listItem.appendChild(img);
                    listItem.appendChild(fileInfo);
                    listItem.appendChild(actions);
                    fileList.appendChild(listItem);
                };
                reader.readAsDataURL(file);
            });

            verificarCantidadImagenes();
        });
    });
</script>

@endsection