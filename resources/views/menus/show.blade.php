@extends('layouts.app')

@section('content_header')

@endsection

@section('content')
<div class="container"> <!-- Un solo contenedor general -->
    <div class="card mb-4 shadow-lg"> <!-- Un solo card para toda la vista -->
        <div class="card-body">

            <!-- Sección de Información del Menú -->
            <h3 class="text-center text-primary font-weight-bold mb-3">Información del Menú</h3>

            <div class="card p-3 shadow-sm"> <!-- Tarjeta contenedora -->
                <div class="row align-items-center text-center">
                    <div class="col-md-4">
                        <p><strong>Nombre:</strong> {{ ucfirst($menu->nombre_menu) }}</p>

                    </div>
                    <div class="col-md-4">
                        <p><strong>Categoría:</strong> {{ ucfirst($menu->categoria) }}</p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Sucursal:</strong> {{ $menu->sucursale->nombre_sucursal ?? 'No asignada' }}</p>
                    </div>
                </div>
            </div> <!-- Fin de la tarjeta -->

            <!-- Mensajes de éxito -->
            @if(session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
            @endif

            <!-- Verificación de productos -->
            @if($productos->isEmpty())
            <div class="alert alert-warning mt-3">No hay productos registrados en este menú.</div>
            @else
            <!-- Título de la sección del carrusel -->
            <h3 class="text-start text-primary font-weight-bold mt-4">Lista de Productos</h3>
            <p class="text-start text-muted mb-4">Cantidad de productos: {{ $productos->count() }}</p>

            <div class="position-relative">

                <!-- Contenedor del carrusel -->
                <div id="productosCarousel" class="d-flex overflow-hidden pb-3 pt-3"
                    style="scroll-behavior: smooth; gap: 20px; flex-wrap: nowrap; white-space: nowrap;">
                    <!-- Corrección aquí -->

                    @foreach($productos as $producto)
                    <div class="card shadow-lg h-100 d-flex flex-column mx-auto"
                        style="min-width: 360px; max-width: 360px; flex-shrink: 0; min-height: auto;">
                        <!-- Altura automática -->

                        <!-- Imagen del producto -->
                        <div class="card-img-top text-center p-3 overflow-hidden" style="max-height: 200px;">
                            @if($producto->imagenes_productos->count() > 1)
                            <div id="carouselProducto{{ $producto->id_producto }}" class="carousel slide"
                                data-ride="carousel">
                                <div class="carousel-inner">
                                    @foreach($producto->imagenes_productos as $index => $imagen)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $imagen->ruta_imagen) }}"
                                            alt="{{ $producto->nombre_producto }}"
                                            class="d-block w-100 rounded img-carousel"
                                            style="height: 200px; object-fit: cover;">
                                    </div>
                                    @endforeach
                                </div>
                                <a class="carousel-control-prev" href="#carouselProducto{{ $producto->id_producto }}"
                                    role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Anterior</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselProducto{{ $producto->id_producto }}"
                                    role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Siguiente</span>
                                </a>
                            </div>
                            @elseif($producto->imagenes_productos->isNotEmpty())
                            <img src="{{ asset('storage/' . $producto->imagenes_productos->first()->ruta_imagen) }}"
                                alt="{{ $producto->nombre_producto }}" class="img-fluid rounded img-carousel"
                                style="width: 100%; height: 200px; object-fit: cover;">
                            @else
                            <img src="{{ asset('images/default-product.png') }}" alt="Sin imagen"
                                class="img-fluid rounded img-carousel"
                                style="width: 100%; height: 200px; object-fit: cover;">
                            @endif
                        </div>

                        <!-- Contenido de la tarjeta -->
                        <div class="card-body flex-grow-1">
                            <h5 class="card-title text-primary">{{ $producto->nombre_producto }}</h5>
                            <p class="card-text"><strong>Descripción:</strong>
                                {{ $producto->descripcion ?? 'Sin descripción' }}
                            </p>
                            <p class="card-text"><strong>Precio:</strong> ${{ number_format($producto->precio, 2) }}</p>
                            <p class="card-text"><strong>Disponibilidad:</strong>
                                <span class="badge {{ $producto->disponible ? 'badge-success' : 'badge-danger' }}">
                                    {{ $producto->disponible ? 'Disponible' : 'No disponible' }}
                                </span>
                            </p>
                        </div>

                        <!-- Footer de la tarjeta con botones alineados -->
                        <div class="card-footer d-flex align-items-center">
                            <!-- Botón "Ver Detalles" centrado -->
                            <div class="flex-grow-1 d-flex justify-content-center">
                                <a href="{{ route('productos.show', $producto->id_producto) }}"
                                    class="btn btn-sm btn-primary">
                                    Ver Detalles
                                </a>
                            </div>

                            <!-- Botón de opciones alineado a la derecha con dropup -->
                            <div class="dropup ml-auto">
                                <button class="btn btn-link p-0" type="button"
                                    id="dropdownMenu{{ $producto->id_producto }}" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right"
                                    aria-labelledby="dropdownMenu{{ $producto->id_producto }}">
                                    <li>
                                        <button class="dropdown-item text-danger delete-btn"
                                            data-menu-id="{{ $menu->id_menu }}"
                                            data-producto-id="{{ $producto->id_producto }}">
                                            <i class="fas fa-trash-alt"></i> Eliminar del menú
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>

                <!-- Botones de navegación -->
                <button id="prevBtn" class="btn btn-dark position-absolute"
                    style="top: 50%; left: 0; transform: translateY(-50%); z-index: 10;">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button id="nextBtn" class="btn btn-dark position-absolute"
                    style="top: 50%; right: 0; transform: translateY(-50%); z-index: 10;">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            @endif
        </div> <!-- Cierre de card-body -->
    </div> <!-- Cierre de Card -->
</div> <!-- Cierre del container -->
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let scrollContainer = document.getElementById('productosCarousel');
        let prevBtn = document.getElementById('prevBtn');
        let nextBtn = document.getElementById('nextBtn');

        // Configurar desplazamiento automático cuando se presionan los botones
        prevBtn.addEventListener('click', function() {
            scrollContainer.scrollBy({
                left: -350,
                behavior: 'smooth'
            });
        });

        nextBtn.addEventListener('click', function() {
            scrollContainer.scrollBy({
                left: 350,
                behavior: 'smooth'
            });
        });
    });

    // Cierra las alertas automáticamente después de 5 segundos
    setTimeout(() => {
        let alertas = document.querySelectorAll('.alert');
        alertas.forEach(alert => {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>

<!-- Incluir SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                let menu_id = this.getAttribute('data-menu-id');
                let producto_id = this.getAttribute('data-producto-id');

                // Confirmación con SweetAlert
                Swal.fire({
                    title: "¿Estás seguro?",
                    text: "Este producto será eliminado del menú, pero no de la base de datos.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Crear y enviar el formulario dinámicamente
                        let form = document.createElement("form");
                        form.action = `/menus/${menu_id}/productos/${producto_id}`;
                        form.method = "POST";

                        let csrfField = document.createElement("input");
                        csrfField.type = "hidden";
                        csrfField.name = "_token";
                        csrfField.value = "{{ csrf_token() }}";
                        form.appendChild(csrfField);

                        let methodField = document.createElement("input");
                        methodField.type = "hidden";
                        methodField.name = "_method";
                        methodField.value = "DELETE";
                        form.appendChild(methodField);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    });
</script>