<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Ventas por Sucursal</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #4CAF50; color: white; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Reporte de Ventas por Sucursal - {{ date('Y') }}</h1>
    <h4>Total de Ventas: ${{ number_format($totalVentas, 2) }}</h4>

    <table>
        <thead>
            <tr>
                <th>ID Pedido</th>
                <th>Fecha del Pedido</th>
                <th>Método de Pago</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedidos as $pedido)
                <tr>
                    <td>{{ $pedido->id_pedido }}</td>
                    <td>{{ $pedido->fecha_pedido }}</td>
                    <td>{{ ucfirst($pedido->metodo_pago) }}</td>
                    <td>${{ number_format($pedido->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
