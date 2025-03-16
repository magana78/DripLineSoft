@extends('adminlte::page')

@section('title', 'Configuración de Seguridad')

@section('content_header')
    <h1 class="text-center"><i class="fas fa-lock"></i> Seguridad de la Cuenta</h1>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4><i class="fas fa-user-shield"></i> Cambios de Seguridad</h4>
                </div>
                <div class="card-body">

                    @if(session('success'))
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: '{{ session("success") }}',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            });
                        </script>
                    @endif

                    @if($errors->any())
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    html: `
                                        <ul style="text-align: left;">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    `,
                                    confirmButtonColor: '#d33',
                                    confirmButtonText: 'Intentar de nuevo'
                                });
                            });
                        </script>
                    @endif

                    <form method="POST" action="{{ route('password.change.update') }}">
                        @csrf  

                        <!-- CAMBIO DE CORREO ELECTRÓNICO -->
                        <div class="mb-3">
                            <label for="email" class="form-label"><i class="fas fa-envelope"></i> Nuevo Correo Electrónico</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        <!-- CAMPO DE CONTRASEÑA ACTUAL PARA AMBOS CAMBIOS -->
                        <div class="mb-3">
                            <label for="current_password" class="form-label"><i class="fas fa-lock"></i> Contraseña Actual (Para Cambiar el Correo o la Contraseña)</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                id="current_password" name="current_password" placeholder="Ingresa tu contraseña actual" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- CAMBIO DE CONTRASEÑA -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label"><i class="fas fa-key"></i> Nueva Contraseña</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                id="new_password" name="new_password">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label"><i class="fas fa-check-double"></i> Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" 
                                id="new_password_confirmation" name="new_password_confirmation">
                            @error('new_password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> Guardar Cambios</button>
                    </form>
                </div>  
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop
