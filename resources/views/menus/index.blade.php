@extends('layouts.app')

@section('title', 'Menús')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-primary">Menú de mis sucursales</h2>

    <!-- Botón flotante para agregar menú -->
    <a href="{{ route('menus.create') }}" class="btn btn-primary shadow rounded-circle"
        style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-plus"></i>
    </a>
</div>
@endsection

@section('content')
<!-- Mensaje de éxito -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if($menus->isEmpty())
<div class="alert alert-warning">
    No hay menús disponibles para tus sucursales.
</div>
@else
<div class="row">
    @foreach ($menus as $menu)
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card shadow-lg border-0 rounded-lg h-100 d-flex flex-column position-relative">

            <!-- Ícono de lápiz flotante -->
            <button class="btn action-btn-menus edit-btn-menus edit-btn-menus-floating"
                data-toggle="modal" data-target="#editMenuModal-{{ $menu->id_menu }}"
                title="Editar">
                <i class="fas fa-edit"></i>
            </button>




            <div class="card-body">
                <h5 class="card-title text-primary">
                    <i class="fas fa-utensils"></i> {{ $menu->nombre_menu }}
                </h5>
                <p class="card-text"><strong><i class="fas fa-list"></i> Categoría:</strong>
                    <span class="badge badge-info">{{ ucfirst($menu->categoria) }}</span>
                </p>
                <p class="card-text"><strong><i class="fas fa-store"></i> Sucursal:</strong>
                    {{ $menu->sucursale->nombre_sucursal }}
                </p>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('menus.show', $menu->id_menu) }}" class="btn btn-sm btn-primary">
                    Ver productos
                </a>
            </div>
        </div>
    </div>

    <!-- Modal de Edición -->
    <div class="modal fade" id="editMenuModal-{{ $menu->id_menu }}" tabindex="-1" aria-labelledby="editMenuModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Menú</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('menus.update', $menu->id_menu) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">

                        <!-- Nombre del Menú -->
                        <div class="form-group">
                            <label for="nombre_menu"><i class="fas fa-tag"></i> Nombre del Menú</label>
                            <input type="text" class="form-control" id="nombre_menu" name="nombre_menu"
                                value="{{ $menu->nombre_menu }}" required>
                        </div>

                        <!-- Categoría -->
                        <div class="form-group">
                            <label for="categoria"><i class="fas fa-list"></i> Categoría</label>
                            <select class="form-control" id="categoria" name="categoria" required>
                                <option value="bebidas calientes" {{ $menu->categoria == 'bebidas calientes' ? 'selected' : '' }}>Bebidas Calientes</option>
                                <option value="bebidas frías" {{ $menu->categoria == 'bebidas frías' ? 'selected' : '' }}>Bebidas Frías</option>
                                <option value="postres" {{ $menu->categoria == 'postres' ? 'selected' : '' }}>Postres</option>
                                <option value="snacks" {{ $menu->categoria == 'snacks' ? 'selected' : '' }}>Snacks</option>
                                <option value="promociones" {{ $menu->categoria == 'promociones' ? 'selected' : '' }}>Promociones</option>
                                <option value="ensaladas" {{ $menu->categoria == 'ensaladas' ? 'selected' : '' }}>Ensaladas</option>
                                <option value="entradas" {{ $menu->categoria == 'entradas' ? 'selected' : '' }}>Entradas</option>
                                <option value="platos fuertes" {{ $menu->categoria == 'platos fuertes' ? 'selected' : '' }}>Platos Fuertes</option>
                                <option value="comida rápida" {{ $menu->categoria == 'comida rápida' ? 'selected' : '' }}>Comida Rápida</option>
                                <option value="carnes" {{ $menu->categoria == 'carnes' ? 'selected' : '' }}>Carnes</option>
                                <option value="mariscos" {{ $menu->categoria == 'mariscos' ? 'selected' : '' }}>Mariscos</option>
                                <option value="sopas y caldos" {{ $menu->categoria == 'sopas y caldos' ? 'selected' : '' }}>Sopas y Caldos</option>
                                <option value="comida mexicana" {{ $menu->categoria == 'comida mexicana' ? 'selected' : '' }}>Comida Mexicana</option>
                                <option value="comida italiana" {{ $menu->categoria == 'comida italiana' ? 'selected' : '' }}>Comida Italiana</option>
                                <option value="comida oriental" {{ $menu->categoria == 'comida oriental' ? 'selected' : '' }}>Comida Oriental</option>
                                <option value="vegetariano" {{ $menu->categoria == 'vegetariano' ? 'selected' : '' }}>Vegetariano</option>
                                <option value="vegano" {{ $menu->categoria == 'vegano' ? 'selected' : '' }}>Vegano</option>
                            </select>
                        </div>


                        <!-- Seleccionar Sucursal -->
                        <div class="form-group">
                            <label for="id_sucursal"><i class="fas fa-store"></i> Seleccionar Sucursal</label>
                            <select class="form-control" id="id_sucursal" name="id_sucursal" required>
                                @foreach ($sucursales as $sucursal)
                                <option value="{{ $sucursal->id_sucursal }}"
                                    {{ $menu->id_sucursal == $sucursal->id_sucursal ? 'selected' : '' }}>
                                    {{ $sucursal->nombre_sucursal }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @endforeach
</div>
@endif
@stop

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