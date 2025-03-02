@extends('layouts.app')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-3"></div>
@endsection

@section('content')
<div class="container mt-4">
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
                    <a href="{{ route('productos.index') }}" class="btn me-2" data-bs-toggle="tooltip" title="Regresar">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h4 class="mb-0">Detalles del Producto</h4>
                </div>
                <div class="card-body">
                    <h3 class="text-primary">{{ $producto->nombre_producto }}</h3>
                    <p><strong>📖 Descripción:</strong> {{ $producto->descripcion }}</p>
                    <p><strong>💰 Precio:</strong> ${{ number_format($producto->precio, 2) }}</p>
                    <p><strong>📌 Disponible:</strong>
                        {!! $producto->disponible
                        ? '<span class="badge badge-success">Sí</span>'
                        : '<span class="badge badge-danger">No</span>' !!}
                    </p>
                    <p><strong>🏪 Sucursal:</strong> {{ $producto->menu->sucursale->nombre_sucursal }}</p>
                    <p><strong>📂 Menú:</strong> {{ $producto->menu->nombre_menu }}</p>

                    <!-- 🔹 Galería de imágenes con tamaño consistente -->
                    @if($producto->imagenes_productos->count() > 1)
                    <div id="carouselProducto" class="carousel slide" data-ride="carousel" style="max-width: 400px; margin: auto;">
                        <ol class="carousel-indicators">
                            @foreach($producto->imagenes_productos as $key => $imagen)
                            <li data-target="#carouselProducto" data-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : '' }}"></li>
                            @endforeach
                        </ol>
                        <div class="carousel-inner">
                            @foreach($producto->imagenes_productos as $key => $imagen)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                <div class="image-container">
                                    <img src="{{ asset('storage/' . $imagen->ruta_imagen) }}" class="d-block w-100 img-fluid" alt="Imagen del producto">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <a class="carousel-control-prev" href="#carouselProducto" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Anterior</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselProducto" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Siguiente</span>
                        </a>
                    </div>

                    @elseif($producto->imagenes_productos->count() == 1)
                    <div class="image-container">
                        <img src="{{ asset('storage/' . $producto->imagenes_productos->first()->ruta_imagen) }}" class="d-block w-100 img-fluid" alt="Imagen del producto">
                    </div>
                    @else
                    <p class="text-muted text-center">📷 No hay imágenes disponibles</p>
                    @endif

                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn action-btn edit-btn mr-2" data-toggle="modal" data-target="#editProductModal" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>

                        <form action="{{ route('productos.toggle', $producto->id_producto) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn action-btn toggle-btn" data-toggle="tooltip" title="Habilitar/Deshabilitar">
                                <i class="fas {{ $producto->disponible ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger' }}"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<!-- Modal de Edición -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Usa modal-lg para mejor diseño -->
        <div class="modal-content card-primary"> <!-- Agrega clase de AdminLTE -->

            <div class="modal-header bg-primary">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Producto</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Formulario de edición -->
                <form action="{{ route('productos.update', $producto->id_producto) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="nombre_producto_edit">Nombre del Producto</label>
                        <input type="text" class="form-control" id="nombre_producto_edit" name="nombre_producto">
                    </div>

                    <div class="form-group">
                        <label for="descripcion_edit">Descripción</label>
                        <textarea class="form-control" id="descripcion_edit" name="descripcion"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="precio_edit">Precio</label>
                        <input type="number" step="0.01" class="form-control" id="precio_edit" name="precio">
                    </div>

                    <div class="form-group">
                        <label for="id_sucursal_edit">Sucursal</label>
                        <select id="id_sucursal_edit" name="id_sucursal" class="form-control">
                            @foreach ($sucursales as $sucursal)
                            <option value="{{ $sucursal->id_sucursal }}">{{ $sucursal->nombre_sucursal }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group d-none" id="menu-container-edit">
                        <label for="id_menu_edit">Menú</label>
                        <div class="position-relative">
                            <div class="input-group">
                                <select id="id_menu_edit" name="id_menu" class="form-control d-none" disabled>
                                    <option value="">Seleccione un menú</option>
                                </select>
                                <div id="menu-spinner-edit" class="spinner-border text-primary d-none" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                            </div>
                        </div>
                        <div id="menu-alert-edit" class="alert alert-warning mt-2 d-none">Esta sucursal no tiene menús registrados.</div>
                    </div>

                    <!-- Imágenes del Producto -->
                    <div class="form-group">
                        <label class="form-label">Imágenes del Producto (Máx. 4)</label>
                        <ul class="list-group mt-3" id="file-list-edit">
                            @foreach($producto->imagenes_productos as $imagen)
                            <li class="list-group-item d-flex justify-content-between align-items-center" id="image-{{ $imagen->id_imagen }}">
                                <img src="{{ asset('storage/' . $imagen->ruta_imagen) }}" class="img-thumbnail mr-2" style="width: 50px; height: 50px; object-fit: cover;">
                                <span><strong>Imagen actual</strong></span>
                                <div>
                                    <!-- Campo de archivo oculto para editar la imagen -->
                                    <input type="file" class="d-none image-input" data-img-id="{{ $imagen->id_imagen }}" name="imagenes[{{ $imagen->id_imagen }}]">
                                    <button type="button" class="btn btn-sm btn-outline-warning edit-image-btn"><i class="fas fa-edit"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-image-btn" data-img-id="{{ $imagen->id_imagen }}"><i class="fas fa-times"></i></button>
                                </div>
                            </li>
                            @endforeach
                        </ul>

                        <!-- Campo de carga de imágenes nuevas -->
                        <input type="file" id="imagenes_edit" name="imagenes_nuevas[]" class="form-control mt-2" accept="image/*" multiple>
                        <small id="file-limit-warning-edit" class="text-danger d-none">⚠️ Máximo de 4 imágenes alcanzado.</small>
                    </div>

                    <!-- Campo oculto para las imágenes eliminadas -->
                    <input type="hidden" id="deleted_images" name="deleted_images" value="">


                    <button type="submit" class="btn btn-primary">Actualizar Producto</button>

                </form>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof $ === "undefined") {
            console.error("Error: jQuery no está cargado. Verifica que jQuery se haya incluido en la plantilla.");
            return;
        }

        $('#editProductModal').on('shown.bs.modal', function() {
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
        document.getElementById('id_sucursal_edit')?.addEventListener('change', function() {
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
                        menuAlert.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error al obtener los menús:', error);
                    alert('Hubo un error al cargar los menús. Inténtelo de nuevo.');
                    menuSpinner.classList.add('d-none');
                });
        }

        document.querySelectorAll('.edit-image-btn').forEach(button => {
            button.addEventListener('click', function() {
                let inputFile = this.previousElementSibling; // El input de archivo oculto
                inputFile.click(); // Simula el click del input

                inputFile.addEventListener('change', function(event) {
                    let file = event.target.files[0];
                    if (file) {
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            // Actualiza la imagen en la vista con la nueva imagen
                            let img = inputFile.closest('li').querySelector('img');
                            img.src = e.target.result; // Muestra la nueva imagen en la interfaz

                            // Asegurar que el archivo será enviado al servidor
                            inputFile.name = `imagenes[${inputFile.dataset.imgId}]`;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        });



        // Función para eliminar imagen
        document.querySelectorAll('.remove-image-btn').forEach(button => {
            button.addEventListener('click', function() {
                let listItem = this.closest('li');
                let imageId = this.getAttribute('data-img-id');
                listItem.remove();

                // Agregar el ID de la imagen eliminada al campo oculto
                let deletedImagesField = document.getElementById('deleted_images');
                let currentDeletedImages = deletedImagesField.value ? deletedImagesField.value.split(',') : [];
                currentDeletedImages.push(imageId);
                deletedImagesField.value = currentDeletedImages.join(',');

                verificarCantidadImagenes();
            });
        });

        // Evento para agregar nuevas imágenes
        document.getElementById('imagenes_edit').addEventListener('change', function(event) {
            let fileList = document.getElementById('file-list-edit');
            let maxFiles = 4;

            Array.from(event.target.files).forEach(file => {
                if (fileList.childElementCount >= maxFiles) {
                    document.getElementById('file-limit-warning-edit').classList.remove('d-none');
                    return;
                }

                let reader = new FileReader();
                reader.onload = function(e) {
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

                    deleteButton.onclick = function() {
                        listItem.remove();

                        // Eliminar la imagen del input file (no la enviará al servidor)
                        let fileInput = document.getElementById('imagenes_edit');
                        fileInput.value = ""; // Vaciar el input file

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

        // Verificar la cantidad de imágenes para no superar el límite
        function verificarCantidadImagenes() {
            let fileInput = document.getElementById('imagenes_edit');
            let fileLimitWarning = document.getElementById('file-limit-warning-edit');
            let fileList = document.getElementById('file-list-edit');

            // Contamos solo las imágenes visibles
            let visibleImages = fileList.querySelectorAll('li:not(.d-none)').length;

            if (visibleImages >= 4) {
                fileInput.classList.add('d-none');
                fileLimitWarning.classList.remove('d-none');
            } else {
                fileInput.classList.remove('d-none');
                fileLimitWarning.classList.add('d-none');
            }
        }


        // Inicialización
        verificarCantidadImagenes();
    });
</script>





@endsection