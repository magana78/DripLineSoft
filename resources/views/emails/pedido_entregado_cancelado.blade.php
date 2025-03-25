<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Estado de tu pedido - DripLine Soft</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
        .card-header img { border-radius: 50%; }
        .status-badge { font-size: 1.5rem; padding: 10px 20px; border-radius: 30px; }
        .status-success { background-color: #28a745; color: #fff; }
        .status-warning { background-color: #ffc107; color: #000; }
        .status-danger { background-color: #dc3545; color: #fff; }
        .list-group-item strong { color: #007bff; }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white text-center py-4">
                <img src="https://i.postimg.cc/4y8VVLJ2/Drip-Line-Soft.png" 
                alt="DripLine Soft Logo" 
                width="100" 
                class="mb-3">
                <h1 class="h4 m-0">Estado de tu pedido</h1>
            </div>

            <div class="card-body">
                <div class="text-center mb-4">
                    <span class="status-badge {{ $accion == 'entregado' ? 'status-success' : ($accion == 'listo' ? 'status-warning' : 'status-danger') }}">
                        {{ ucfirst($accion) }}
                    </span>
                </div>

                <p class="text-center">
                    Hola, <strong>{{ $pedido->cliente ? $pedido->cliente->nombre : 'Cliente no registrado' }}</strong>.
                </p>

                @if($accion == 'entregado')
                    <p class="text-center text-success">✅ Tu pedido ha sido entregado.</p>
                    <p class="text-center"><strong>📅 Fecha de Entrega:</strong> {{ \Carbon\Carbon::parse($pedido->fecha_entregado)->format('d/m/Y H:i') }}</p>
                @elseif($accion == 'listo')
                    <p class="text-center text-warning">🚀 Tu pedido está listo y puedes recogerlo en cualquier momento.</p>
                    <p class="text-center"><strong>📅 Fecha del Pedido:</strong> {{ \Carbon\Carbon::parse($pedido->fecha_pedido)->format('d/m/Y H:i') }}</p>
                @else
                    <p class="text-center text-danger">❌ Lamentablemente, tu pedido ha sido cancelado.</p>
                @endif

                <hr>

                <div class="text-start">
                    <h4 class="text-primary">📋 Detalles del Pedido:</h4>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>ID Pedido:</strong> {{ $pedido->id_pedido }}</li>
                        <li class="list-group-item"><strong>Estado:</strong> {{ ucfirst($pedido->estado) }}</li>
                        <li class="list-group-item"><strong>Total:</strong> ${{ number_format($pedido->total, 2) }}</li>
                    </ul>
                </div>

                <div class="text-center my-4">
                    <p class="text-muted">Gracias por confiar en nosotros. Si tienes alguna pregunta, no dudes en contactarnos.</p>
                    <p class="fw-bold">Atentamente, el equipo de <span class="text-primary">DripLine Soft</span>.</p>
                </div>
            </div>

            <div class="card-footer text-center bg-dark text-white">
                © {{ date('Y') }} DripLine Soft. Todos los derechos reservados.
            </div>
        </div>
    </div>
</body>
</html>
