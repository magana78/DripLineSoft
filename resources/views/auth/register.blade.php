@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('title', 'Registro de Usuario y Negocio')

@section('auth_body')
    <form action="/registro" method="POST">
        @csrf

        <!-- Información del Usuario -->
        <h4 class="text-primary mb-3"><i class="fas fa-user icon"></i> Datos del Usuario</h4>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" id="nombre" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select id="rol" name="rol" class="form-control select2" required>
                <option value="admin_sistema">Administrador del Sistema</option>
                <option value="admin_cliente">Administrador del Cliente</option>
                <option value="cliente_final">Cliente Final</option>
            </select>
        </div>

        <!-- Información del Negocio -->
        <h4 class="text-primary mt-4 mb-3"><i class="fas fa-store icon"></i> Datos del Negocio</h4>

        <div class="mb-3">
            <label for="nombre_comercial" class="form-label">Nombre Comercial</label>
            <input type="text" id="nombre_comercial" name="nombre_comercial" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" id="direccion" name="direccion" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" id="telefono" name="telefono" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email_contacto" class="form-label">Email de Contacto</label>
            <input type="email" id="email_contacto" name="email_contacto" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="plan_suscripcion" class="form-label">Plan de Suscripción</label>
            <select id="plan_suscripcion" name="plan_suscripcion" class="form-control select2" required>
                <option value="mensual">Mensual</option>
                <option value="anual">Anual</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="monto_suscripcion" class="form-label">Monto de Suscripción</label>
            <input type="number" id="monto_suscripcion" name="monto_suscripcion" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="fecha_registro" class="form-label">Fecha de Registro</label>
            <input type="date" id="fecha_registro" name="fecha_registro" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="fecha_fin_suscripcion" class="form-label">Fecha de Fin de Suscripción</label>
            <input type="date" id="fecha_fin_suscripcion" name="fecha_fin_suscripcion" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="estado_suscripcion" class="form-label">Estado de Suscripción</label>
            <select id="estado_suscripcion" name="estado_suscripcion" class="form-control select2" required>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="sector" class="form-label">Sector</label>
            <select id="sector" name="sector" class="form-control select2" required>
                <option value="cafetería">Cafetería</option>
                <option value="restaurante">Restaurante</option>
                <option value="otro">Otro</option>
            </select>
        </div>

        <!-- Pago con PayPal -->
        <h4 class="text-primary mt-4 mb-3"><i class="fab fa-paypal icon"></i> Pago con PayPal</h4>

        <div class="mb-3">
            <label for="paypal_email" class="form-label">Correo Electrónico de PayPal</label>
            <input type="email" id="paypal_email" name="paypal_email" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Registrar Negocio</button>
    </form>
@endsection

@section('auth_footer')
    <p class="text-center mt-2">
        <a href="{{ route('login') }}">¿Ya tienes cuenta? Inicia sesión</a>
    </p>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $('.select2').select2(); // Inicializamos los selects con Select2
        });
    </script>
@stop
