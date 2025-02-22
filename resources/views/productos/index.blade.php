@extends('layouts.app')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary">Lista de Productos</h2>

        <!-- Botón flotante para agregar producto -->
        <a href="{{ route('productos.create') }}" class="btn btn-primary shadow rounded-circle"
            style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-plus"></i>
        </a>
    </div>
@endsection

@section('content')

    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Verificar si hay productos -->
    @if($productos->isEmpty())
        <div class="alert alert-warning">No hay productos registrados.</div>
    @else
        <div class="row">
            @foreach($productos as $producto)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow-lg h-100 d-flex flex-column">
                        <div class="card-img-top text-center p-3" style="height: 200px; overflow: hidden;">
                            @if($producto->imagenes_productos->count() > 1)
                                <!-- Carousel de Bootstrap 4 -->
                                <div id="carouselProducto{{ $producto->id_producto }}" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                        @foreach($producto->imagenes_productos as $index => $imagen)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ asset('storage/' . $imagen->ruta_imagen) }}"
                                                    alt="{{ $producto->nombre_producto }}" class="d-block w-100 rounded img-carousel"
                                                    style="height: 200px; object-fit: cover;">
                                            </div>
                                        @endforeach
                                    </div>
                                    <!-- Controles del carousel -->
                                    <a class="carousel-control-prev" href="#carouselProducto{{ $producto->id_producto }}" role="button"
                                        data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Anterior</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carouselProducto{{ $producto->id_producto }}" role="button"
                                        data-slide="next">
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
                                    class="img-fluid rounded img-carousel" style="width: 100%; height: 200px; object-fit: cover;">
                            @endif
                        </div>

                        <div class="card-body flex-grow-1">
                            <h5 class="card-title text-primary">{{ $producto->nombre_producto }}</h5>
                            <p class="card-text"><strong>Descripción:</strong> {{ $producto->descripcion ?? 'Sin descripción' }}</p>
                            <p class="card-text"><strong>Precio:</strong> ${{ number_format($producto->precio, 2) }}</p>
                            <p class="card-text"><strong>Disponibilidad:</strong>
                                <span class="badge {{ $producto->disponible ? 'badge-success' : 'badge-danger' }}">
                                    {{ $producto->disponible ? 'Disponible' : 'No disponible' }}
                                </span>
                            </p>
                            <p class="card-text"><strong>Sucursal:</strong>
                                {{ $producto->menu->sucursale->nombre_sucursal ?? 'No asignada' }}</p>
                            <p class="card-text"><strong>Menú:</strong> {{ $producto->menu->nombre_menu ?? 'No asignado' }}</p>

                        </div>
                        <div class="card-footer text-center">
                            <a href="{{ route('productos.show', $producto->id_producto) }}" class="btn btn-sm btn-primary">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

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