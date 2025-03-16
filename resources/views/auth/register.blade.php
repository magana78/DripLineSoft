@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('title', 'Registro de Usuario y Negocio')

@section('auth_body')
    <form id="registro-form" action="/registro" method="POST">
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
            <label for="sector" class="form-label">Sector</label>
            <select id="sector" name="sector" class="form-control select2" required>
                <option value="cafetería">Cafetería</option>
                <option value="restaurante">Restaurante</option>
                <option value="otro">Otro</option>
            </select>
        </div>

        <!-- Campos ocultos para registrar el pago -->
        <input type="hidden" id="estado_suscripcion" name="estado_suscripcion" value="pendiente">
        <input type="hidden" id="monto_suscripcion" name="monto_suscripcion" value="0.00">

        <!-- Pago con PayPal -->
        <h4 class="text-primary mt-4 mb-3"><i class="fab fa-paypal icon"></i> Pago con PayPal</h4>

        <p>Para completar el registro, realiza el pago de <strong>$300 MXN</strong> a través de PayPal.</p>

        <div id="paypal-button-container"></div> <!-- Contenedor del botón oficial -->

        <!-- Botón para registrar (deshabilitado hasta completar el pago) -->
        <div class="mb-3 text-center mt-4">
            <button type="submit" id="btn-registrar" class="btn btn-primary" >
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
    <script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=MXN"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            console.log("✅ Verificando carga del SDK de PayPal...");
    
            // 💡 Cuando se envía el formulario de registro
            document.getElementById("registro-form").addEventListener("submit", function (e) {
                e.preventDefault(); // 🔹 Evita que recargue la página
    
                let formData = new FormData(this);
    
                fetch("/registro", {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // ✅ Alerta con SweetAlert (Recomendado) 
                        Swal.fire({
                            icon: 'success',
                            title: 'Registro Exitoso',
                            text: 'Tu cuenta ha sido creada. Ahora procede con el pago para activarla.',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            // Guardar email para usarlo en el pago
                            localStorage.setItem("email_contacto", data.email_contacto);
    
                            // Mostrar botón de PayPal
                            document.getElementById("paypal-button-container").style.display = "block";
                            document.getElementById("btn-registrar").style.display = "none"; // Ocultar botón de registro
    
                            // Renderizar el botón de PayPal
                            renderPayPalButton();
                        });
    
                    } else {
                        // ❌ Alerta de error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error en el registro',
                            text: data.message
                        });
                    }
                })
                .catch(error => console.error("❌ Error en la petición:", error));
            });
    
            function renderPayPalButton() {
                if (typeof paypal !== "undefined") {
                    paypal.Buttons({
                        style: {
                            layout: 'vertical',
                            color: 'gold',
                            shape: 'rect',
                            label: 'pay',
                            height: 40
                        },
                        createOrder: function (data, actions) {
                            return actions.order.create({
                                purchase_units: [{
                                    amount: { value: '300.00' }
                                }]
                            });
                        },
                        onApprove: function (data, actions) {
                            return actions.order.capture().then(function (details) {
                                console.log("✅ Pago aprobado:", details);
    
                                let emailContacto = localStorage.getItem("email_contacto");
    
                                if (!emailContacto) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'No se encontró el email de contacto.'
                                    });
                                    return;
                                }
    
                                fetch('/paypal/capture-order', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({
                                        orderID: data.orderID,
                                        email_contacto: emailContacto // 🔹 Enviamos el email almacenado
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Pago Completado',
                                            text: 'Tu suscripción ha sido activada.',
                                            confirmButtonText: 'Ir al Inicio de Sesión'
                                        }).then(() => {
                                            window.location.href = "/dashboard";
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error en el pago',
                                            text: data.error
                                        });
                                    }
                                });
                            });
                        },
                        onError: function (err) {
                            console.error('❌ Error en el pago:', err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error en el proceso de pago.'
                            });
                        }
                    }).render('#paypal-button-container');
                } else {
                    console.error("❌ Error: PayPal SDK no cargó correctamente.");
                }
            }
        });
    </script>
    
    <!-- Agregar SweetAlert (si no lo tienes) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
@stop
