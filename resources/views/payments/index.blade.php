@extends('adminlte::page')

@section('title', 'Métodos de Pago')

@section('content_header')
    <h1><i class="fas fa-credit-card"></i> Métodos de Pago</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header"><i class="fas fa-wallet"></i> Mis Métodos de Pago</div>
        <div class="card-body">
            @if($paymentMethods->isEmpty())
                <p class="text-center text-muted"><i class="fas fa-exclamation-circle"></i> No tienes métodos de pago registrados.</p>
                <div class="text-center">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addPaymentMethodModal">
                        <i class="fas fa-plus"></i> Agregar Métodos de Pago
                    </button>
                </div>
            @else
                <ul class="list-group">
                    @foreach($paymentMethods as $method)
                        <li class="list-group-item"><i class="fas fa-money-bill-wave"></i> {{ $method->nombre_metodo }}</li>
                    @endforeach
                </ul>
                <div class="mt-3 text-center">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addPaymentMethodModal">
                        <i class="fas fa-plus"></i> Agregar Más Métodos
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addPaymentMethodModal" tabindex="-1" aria-labelledby="addPaymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-credit-card"></i> Seleccionar Métodos de Pago</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('payments.store') }}">
                    @csrf
                    <div class="modal-body">
                        @foreach($allPaymentMethods as $method)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="methods[]" value="{{ $method->id_metodo_pago }}" 
                                    {{ in_array($method->id_metodo_pago, $userPaymentMethods) ? 'checked' : '' }}>
                                <label class="form-check-label"><i class="fas fa-money-check-alt"></i> {{ $method->nombre_metodo }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
