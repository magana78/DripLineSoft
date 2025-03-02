@extends('adminlte::page')

@section('title', 'Perfil de Usuario')

@section('content_header')
    <h1>Perfil</h1>
@stop

@section('content')
<div class="container">
    <div class="row">
        <!-- Información del Usuario -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Actualizar Perfil</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- Si la membresía está vencida -->
                    @if(isset($cliente) && (trim($cliente->estado_suscripcion) === 'pendiente' || trim($cliente->estado_suscripcion) === 'cancelado'))
                    <div class="alert alert-warning text-center">
                        <strong>⚠ Tu membresía ha expirado.</strong><br>
                        Debes renovarla para acceder a todas las funciones.
                    </div>
                    <div class="text-center">
                        <div id="paypal-button-container"></div>
                    </div>
                @else
                    <form action="{{ route('perfil.update') }}" method="POST" id="perfil-form">
                        @csrf
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="text" name="nombre" class="form-control" value="{{ $usuario->name }}" required>
                        </div>
                        <div class="form-group">
                            <label>Nombre Comercial:</label>
                            <input type="text" name="nombre_comercial" class="form-control" value="{{ $cliente->nombre_comercial ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label>Dirección:</label>
                            <input type="text" name="direccion" class="form-control" value="{{ $cliente->direccion ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label>Teléfono:</label>
                            <input type="text" name="telefono" class="form-control" value="{{ $cliente->telefono ?? '' }}">
                        </div>
                        <button type="submit" class="btn btn-success">Actualizar Perfil</button>
                    </form>
                @endif
                
                
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<!-- PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id={{ config('services.paypal.client_id') }}&currency=MXN"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        console.log("✅ Verificando carga del SDK de PayPal...");

        @if(isset($cliente) && (trim($cliente->estado_suscripcion) === 'pendiente' || trim($cliente->estado_suscripcion) === 'cancelado'))
            console.log("🟡 La membresía está vencida, renderizando PayPal...");
            setTimeout(() => {
                renderPayPalButton();
            }, 1000);
        @else
            console.log("✅ La membresía está activa, no se muestra PayPal.");
        @endif

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

                            fetch("{{ route('perfil.renovar') }}", {
                                method: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify({ pago_exitoso: true })
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log("📌 Respuesta del servidor:", data);

                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Renovación Completada',
                                        text: 'Tu suscripción ha sido renovada exitosamente. ¡Puedes seguir disfrutando del servicio!',
                                        confirmButtonText: 'Ir al Dashboard'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error en la renovación',
                                        text: data.message
                                    });
                                }
                            })
                            .catch(error => {
                                console.error("❌ Error en la solicitud a Laravel:", error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Ocurrió un problema al actualizar la membresía.'
                                });
                            });
                        });
                    }
                }).render('#paypal-button-container');
            } else {
                console.error("❌ Error: PayPal SDK no cargó correctamente.");
            }
        }
    });
</script>



<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stop
