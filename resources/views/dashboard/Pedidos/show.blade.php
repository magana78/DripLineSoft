@extends('adminlte::page')

@section('title', 'Detalles del Pedido')

@section('content')

<div class="container">
    <div class="card shadow">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h4 class="text-primary m-0">Información del Pedido</h4>

            <!-- Grupo de botones flotantes -->
            <div class="btn-group">
                <button class="btn btn-outline-primary shadow rounded-circle ml-2" data-toggle="modal" data-target="#cambiarEstadoModal" title="Cambiar Estado" style="width: 50px; height: 50px;">
                    <i class="fas fa-exchange-alt"></i>
                </button>
            
                <button class="btn btn-outline-danger shadow rounded-circle ml-2" data-toggle="modal" data-target="#cancelarPedidoModal" title="Cancelar Pedido" style="width: 50px; height: 50px;">
                    <i class="fas fa-times"></i>
                </button>
            
                <button class="btn btn-outline-success shadow rounded-circle ml-2" data-toggle="modal" data-target="#cambiarTiempoModal" title="Modificar Tiempo" style="width: 50px; height: 50px;">
                    <i class="fas fa-clock"></i>
                </button>
            </div>
            
        </div>

        <div class="card-body">
            <hr>
            <p><strong>📅 Fecha del Pedido:</strong> {{ \Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y H:i') }}</p>
            <p><strong>💳 Método de Pago:</strong> {{ ucfirst($pedido->metodo_pago) }}</p>
            <p><strong>🔄 Estado:</strong> 
                <span class="badge 
                    {{ $pedido->estado == 'pendiente' ? 'badge-warning' : 
                        ($pedido->estado == 'en preparación' ? 'badge-info' : 
                        ($pedido->estado == 'listo' ? 'badge-success' : 'badge-danger')) }}">
                    {{ ucfirst($pedido->estado) }}
                </span>
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

            <h4 class="text-primary mt-4">⏳ Tiempo de Entrega</h4>
<hr>
<div id="tiempo-entrega">
    @if ($pedido->estado === 'listo')
        <p class="text-success font-weight-bold text-center p-2">
            🚀 <span class="badge badge-success">¡Puede pasar por su pedido!</span> 🚀
        </p>
    @else
        <p class="text-warning font-weight-bold text-center p-2">
            ⏳ Tiempo restante: <span id="tiempo-restante" class="badge badge-warning">{{ $pedido->tiempo_entrega_estimado }} minutos</span>
        </p>
    @endif
</div>




            <div class="d-flex justify-content-center mt-4">
                <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Pedidos
                </a>
            </div>
        </div>
    </div>
</div>

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

    

@stop
