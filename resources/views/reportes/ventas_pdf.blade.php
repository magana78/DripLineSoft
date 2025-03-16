<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Ventas - Pedidos Finalizados</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #4CAF50; color: white; }
        h1 { text-align: center; color: #4CAF50; }
    </style>
</head>
<body>
    <h1>Reporte de Ventas - {{ date('Y') }}</h1>
    <p>Mes Seleccionado: {{ DateTime::createFromFormat('!m', $mesSeleccionado)->format('F') }}</p>

    <table>
        <thead>
            <tr>
                <th>Fecha del Pedido</th>
                <th>Total</th>
                <th>Método de Pago</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventasPorMes as $venta)
                <tr>
                    <td>{{ $venta->fecha_pedido }}</td>
                    <td>${{ number_format($venta->total, 2) }}</td>
                    <td>{{ ucfirst($venta->metodo_pago) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No hay ventas registradas en este mes.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total" style="text-align: right; margin-top: 10px;">
        <p><strong>Total de Ventas del Mes:</strong> ${{ number_format($totalVentas, 2) }}</p>
        <p><strong>Cantidad de Pedidos Atendidos:</strong> {{ $ventasPorMes->count() }}</p>
    </div>
</body>
</html>
