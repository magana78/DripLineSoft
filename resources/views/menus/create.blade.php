@extends('adminlte::page')

@section('title', 'Crear Menú')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-primary">Crear Nuevo Menú</h2>
    </div>
@endsection

@section('content')
<!-- Mensajes de error -->
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-lg">
    <div class="card-body">
        <form action="{{ route('menus.store') }}" method="POST">
            @csrf

            <!-- Nombre del Menú -->
            <div class="form-group">
                <label for="nombre_menu"><i class="fas fa-tag"></i> Nombre del Menú</label>
                <input type="text" class="form-control" id="nombre_menu" name="nombre_menu" placeholder="Ejemplo: Desayuno Especial" required>
            </div>

            <!-- Categoría -->
            <div class="form-group">
                <label for="categoria"><i class="fas fa-list"></i> Categoría</label>
                <select class="form-control" id="categoria" name="categoria" required>
                    <option value="" disabled selected>Selecciona una categoría</option>
                    <option value="bebidas calientes">Bebidas Calientes</option>
                    <option value="bebidas frías">Bebidas Frías</option>
                    <option value="postres">Postres</option>
                    <option value="snacks">Snacks</option>
                    <option value="promociones">Promociones</option>
                </select>
            </div>

            <!-- Seleccionar Sucursal -->
            <div class="form-group">
                <label for="id_sucursal"><i class="fas fa-store"></i> Seleccionar Sucursal</label>
                <select class="form-control" id="id_sucursal" name="id_sucursal" required>
                    <option value="" disabled selected>Selecciona una sucursal</option>
                    @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id_sucursal }}">{{ $sucursal->nombre_sucursal }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Botón de Enviar -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Menú</button>
            </div>
        </form>
    </div>
</div>
@endsection
