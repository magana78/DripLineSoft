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
            <label for="fecha_registro" class="form-label">Fecha de Registro</label>
            <input type="date" id="fecha_registro" name="fecha_registro" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="fecha_fin_suscripcion" class="form-label">Fecha de Fin de Suscripción</label>
            <input type="date" id="fecha_fin_suscripcion" name="fecha_fin_suscripcion" class="form-control" required>
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

        <p>Para completar el registro, realiza el pago de <strong>$300 MXN</strong> a través de PayPal.</p>

        <div id="paypal-button-container"></div> <!-- Contenedor del botón oficial -->

        <!-- Botón para registrar -->
        <div class="mb-3 text-center mt-4">
            <button type="submit" class="btn btn-primary">
                Registrar
            </button>
        </div>
    </form>
@endsection

@section('auth_footer')
    <p class="text-center mt-2">
        <a href="{{ route('login') }}">¿Ya tienes cuenta? Inicia sesión</a>
    </p>
@endsection

@section('js')
    <!-- Script oficial de PayPal -->
    <script src="https://www.paypal.com/sdk/js?client-id=AUbPWY96LNcJW662sREzgkRXE15C_-CynMnQywQyr7qgQzfC6RWzyiyZNyPisBVCAQY85kZyNzx-3euu&currency=MXN"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            console.log("Verificando carga del SDK de PayPal...");
            setTimeout(function() {
                if (typeof paypal !== "undefined") {
                    console.log("PayPal SDK cargado. Renderizando el botón...");

                    paypal.Buttons({
                        style: {
                            layout: 'vertical',
                            color: 'gold',
                            shape: 'rect',
                            label: 'pay',
                            height: 40
                        },
                        createOrder: function(data, actions) {
                            return actions.order.create({
                                purchase_units: [{
                                    amount: { value: '300.00' }
                                }]
                            });
                        },
                        onApprove: function(data, actions) {
                            return actions.order.capture().then(function(details) {
                                console.log("Pago aprobado:", details);
                                fetch('/paypal/capture', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        orderID: data.orderID,
                                        payerID: data.payerID,
                                        paymentID: details.id,
                                        status: details.status
                                    })
                                }).then(response => response.json())
                                  .then(data => {
                                      if (data.success) {
                                          alert('Pago completado con éxito');
                                          window.location.href = "/dashboard";
                                      } else {
                                          alert('Error en el pago');
                                      }
                                  });
                            });
                        },
                        onError: function (err) {
                            console.error('Error en el pago:', err);
                            alert('Ocurrió un error en el proceso de pago.');
                        }
                    }).render('#paypal-button-container');
                } else {
                    console.error("Error: PayPal SDK no cargó correctamente.");
                }
            }, 1500);
        });
    </script>
@stop
