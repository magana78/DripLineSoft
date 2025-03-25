@extends('adminlte::page')

@section('title', 'Detalles del Pedido')

@section('content')

<div class="container">
    <div class="card shadow">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h4 class="text-primary m-0">Información del Pedido</h4>

        <!-- Grupo de botones flotantes -->
        @if(!isset($ocultarOpciones) || !$ocultarOpciones)
        <div class="btn-group">
            <button class="btn btn-outline-primary shadow rounded-circle ml-2"
                    data-toggle="modal"
                    data-target="#cambiarEstadoModal"
                    title="Cambiar Estado"
                    style="width: 50px; height: 50px;">
                <i class="fas fa-exchange-alt"></i>
            </button>
    
            <button class="btn btn-outline-danger shadow rounded-circle ml-2"
                    data-toggle="modal"
                    data-target="#cancelarPedidoModal"
                    title="Cancelar Pedido"
                    style="width: 50px; height: 50px;">
                <i class="fas fa-times"></i>
            </button>
    
            <button 
                class="btn btn-outline-success shadow rounded-circle ml-2"
                data-toggle="modal"
                data-target="#listoPedidoModal"
                title="Marcar como Listo"
                style="width: 50px; height: 50px;">
                <i class="fas fa-check"></i>
            </button>
    
            <button class="btn btn-outline-success shadow rounded-circle ml-2"
                    data-toggle="modal"
                    data-target="#cambiarTiempoModal"
                    title="Modificar Tiempo"
                    style="width: 50px; height: 50px;">
                <i class="fas fa-clock"></i>
            </button>
        </div>
    
      
    @endif
    
        </div>

        <div class="card-body">
            <hr>
            <p><strong>📅 Fecha del Pedido:</strong> {{ \Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y H:i') }}</p>
            <p><strong>💳 Método de Pago:</strong> {{ ucfirst($pedido->metodo_pago) }}</p>
            <p><strong>🔄 Estado:</strong> 
                <span class="badge 
                {{ $pedido->estado == 'pendiente' ? 'badge-warning' : 
                    ($pedido->estado == 'en preparación' ? 'badge-info' : 
                    ($pedido->estado == 'listo' || $pedido->estado == 'entregado' ? 'badge-success' : 'badge-danger')) }}">
                {{ ucfirst($pedido->estado) }}
            </span>
            
            </p>
            <p><strong>📅 Fecha de Entrega:</strong> 
                @if ($pedido->fecha_entregado)
                    {{ \Carbon\Carbon::parse($pedido->fecha_entregado)->format('d/m/Y H:i') }}
                @else
                    <span>No entregado aún</span>
                @endif
            </p>
            <p><strong>💰 Total:</strong> ${{ number_format($pedido->total, 2) }}</p>
            <p><strong>💸 Descuento:</strong> ${{ number_format($pedido->descuento, 2) }}</p>
            <p><strong>📝 Nota:</strong> {{ $pedido->nota }}</p>

            <h4 class="text-primary mt-4">🛒 Productos del Pedido</h4>
            <hr>
            @if($detalles->isEmpty())
                <div class="alert alert-warning">No hay productos en este pedido.</div>
            @else
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->nombre_producto }}</td>
                            <td>{{ $detalle->cantidad }}</td>
                            <td>${{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <h4 class="text-primary mt-4">💳 Pago</h4>
            <div class="card border-primary shadow-sm">
                <div class="card-body">
                    <h5 class="text-center text-primary mb-3">🧾 Recibo de Pago</h5>
            
                    <!-- Subtotal y Descuento -->
                    <div class="d-flex justify-content-between">
                        <span><strong>Subtotal:</strong></span>
                        <span>${{ number_format($pedido->total, 2) }}</span>
                    </div>
            
                    <div class="d-flex justify-content-between text-success">
                        <span><strong>Descuento Aplicado:</strong></span>
                        <span>- ${{ number_format($pedido->descuento, 2) }}</span>
                    </div>
            
                    <hr class="my-2">
            
                    <!-- Total a Pagar -->
                    <div class="d-flex justify-content-between font-weight-bold text-dark">
                        <span><strong>Total a Pagar:</strong></span>
                        <span id="total-a-pagar">${{ number_format($pedido->total - $pedido->descuento, 2) }}</span>
                    </div>
            
                    <hr class="my-2">
            
                    <!-- Método de Pago -->
                    <div class="form-group">
                        <label for="metodo-pago"><strong>💳 Método de Pago:</strong></label>
                        <select id="metodo-pago" class="form-control">
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta de Crédito/Débito</option>
                            <option value="transferencia">Transferencia Bancaria</option>
                        </select>
                    </div>
            
                    <!-- Monto Recibido -->
                    <div class="form-group">
                        <label for="monto-recibido"><strong>💵 Monto Recibido:</strong></label>
                        <input 
                            type="number" 
                            id="monto-recibido" 
                            class="form-control" 
                            min="0" 
                            placeholder="Ingrese el monto recibido"
                            oninput="calcularCambio()"
                        >
                    </div>
            
                    <!-- Cambio Devuelto -->
                    <div class="alert alert-info text-center mt-3">
                        <strong>💰 Cambio Devuelto:</strong> <span id="cambio-devuelto">$0.00</span>
                    </div>
            
                    @if(!in_array($pedido->estado, ['entregado', 'cancelado']))
                    <div class="text-center mt-3">
                        <button 
                            id="btnPagar"
                            class="btn btn-success btn-lg"
                            onclick="procesarPago()"
                        >
                            💸 Pagar y Entregar Pedido
                        </button>
                    </div>
                @endif
                
                         
                        <!-- Fecha de Pago -->
                    <div class="text-center mt-3">
                        <span class="text-muted"><em>📅 Fecha de Pago: {{ now()->format('d/m/Y H:i') }}</em></span>
                    </div>
            
                    <div class="alert alert-warning mt-3 text-center" role="alert">
                        💬 Si tienes alguna duda sobre tu pago, no dudes en contactarnos.
                    </div>

                    <h4 class="text-primary mt-4">⏳ Tiempo de Entrega</h4>
                    <hr>
                    <div id="tiempo-entrega" class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                    
                            @if ($pedido->estado === 'entregado')
                                <div class="alert alert-success text-center p-3">
                                    <h5 class="font-weight-bold">
                                        🚀 ¡Tu pedido está listo! 🚀
                                    </h5>
                                    <p class="mb-0">
                                        Puedes pasar a recogerlo en cualquier momento.
                                    </p>
                                </div>
                            @else
                                <div class="alert alert-warning text-center p-3">
                                    <h5 class="font-weight-bold">
                                        ⏳ Tiempo restante para entrega
                                    </h5>
                                    <p class="mb-0">
                                        <span class="badge badge-warning" style="font-size: 1.1rem;">
                                            {{ $pedido->tiempo_entrega_estimado }} minutos
                                        </span>
                                    </p>
                                </div>
                            @endif
                    
                        </div>
                    </div>
                    
            </div>
            
            <!-- SweetAlert -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            
            <script>
                function calcularCambio() {
                    const totalAPagar = parseFloat("{{ $pedido->total - $pedido->descuento }}");
                    const montoRecibido = parseFloat(document.getElementById('monto-recibido').value);
            
                    let cambio = montoRecibido - totalAPagar;
            
                    if (isNaN(cambio) || cambio < 0) {
                        document.getElementById('cambio-devuelto').textContent = "$0.00";
                    } else {
                        document.getElementById('cambio-devuelto').textContent = `$${cambio.toFixed(2)}`;
                    }
                }
            
                function procesarPago() {
    const totalAPagar = parseFloat("{{ $pedido->total - $pedido->descuento }}");
    const montoRecibido = parseFloat(document.getElementById('monto-recibido').value);
    const metodoPago = document.getElementById('metodo-pago').value;

    if (!montoRecibido || montoRecibido < totalAPagar) {
        Swal.fire({
            icon: 'error',
            title: 'Pago insuficiente',
            text: 'El monto recibido es menor que el total a pagar.',
            confirmButtonText: 'Entendido'
        });
        return;
    }

        // 🔒 Deshabilitar todos los botones para evitar clics múltiples
        document.querySelectorAll('button').forEach(btn => btn.disabled = true);

    // Solicitud AJAX para entregar el pedido
    fetch("{{ route('pedidos.entregar', $pedido->id_pedido) }}", {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            metodo_pago: metodoPago,
            monto_recibido: montoRecibido
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al procesar el pago.');
        }
        return response.json();
    })
    .then(data => {
        Swal.fire({
            icon: 'success',
            title: '¡Pago Realizado!',
            text: `Método de pago: ${metodoPago.toUpperCase()}. Cambio devuelto: $${data.cambio_devuelto.toFixed(2)}`,
            confirmButtonText: 'Aceptar'
        }).then(() => {
            window.location.href = "{{ route('pedidos.index') }}";
        });
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al procesar el pago. Inténtalo nuevamente.',
            confirmButtonText: 'Aceptar'
        });
        console.error('Error:', error);

        
    });
}

            </script>
            
            

<!-- Modales de Bootstrap 4 -->

<!-- Modal para cambiar estado -->
<div class="modal fade" id="cambiarEstadoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Cambiar Estado del Pedido</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('pedidos.update', ['id' => $pedido->id_pedido]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <label for="estado">Seleccione un nuevo estado:</label>
                    <select name="estado" id="estado" class="form-control">
                        <option value="pendiente">Pendiente</option>
                        <option value="en preparación">En Preparación</option>
                        <option value="listo">Listo</option>
                        <option value="cancelado">Cancelado</option>
                        <option value="entregado">Entregado</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal para Marcar Pedido como Listo -->
<div class="modal fade" id="listoPedidoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">¿Confirmar que el pedido está listo?</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>El pedido será marcado como "Listo" y se notificará al cliente que puede recogerlo. ¿Deseas continuar?</p>
            </div>
            <div class="modal-footer">
                <form id="formListoPedido" action="{{ route('pedidos.listo', ['id' => $pedido->id_pedido]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="confirmarPedidoBtn">Confirmar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script para Mejorar el Comportamiento del Modal -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('formListoPedido').addEventListener('submit', function () {
            $('#listoPedidoModal').modal('hide'); // Cierra el modal después de enviar
        });

        // Asegura que el botón "Confirmar" siempre esté habilitado
        document.getElementById('confirmarPedidoBtn').disabled = false;
    });
</script>




<!-- Modal para cancelar pedido -->
<div class="modal fade" id="cancelarPedidoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Cancelar Pedido</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('pedidos.cancel', ['id' => $pedido->id_pedido]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas cancelar este pedido?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Volver</button>
                    <button type="submit" class="btn btn-danger">Sí, Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para cambiar tiempo de entrega -->
<div class="modal fade" id="cambiarTiempoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Cambiar Tiempo de Entrega</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('pedidos.updateTime', ['id' => $pedido->id_pedido]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <label for="tiempo">Nuevo tiempo estimado (minutos):</label>
                    <input type="number" name="tiempo_entrega_estimado" class="form-control" min="1" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            confirmButtonText: 'Aceptar'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: '{{ session('error') }}',
            confirmButtonText: 'Aceptar'
        });
    @endif
</script>


@stop
