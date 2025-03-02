<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetallesPedidoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Obtener todos los pedidos y productos disponibles
        $pedidos = DB::table('pedidos')->get();
        $productos = DB::table('productos')->get();

        if ($pedidos->isEmpty() || $productos->isEmpty()) {
            echo "⚠️ No se encontraron pedidos o productos. Agrega datos primero.\n";
            return;
        }

        foreach ($pedidos as $pedido) {
            // Cada pedido tendrá entre 1 y 3 productos
            $productosSeleccionados = $productos->random(rand(1, 3));

            foreach ($productosSeleccionados as $producto) {
                $cantidad = rand(1, 5);
                DB::table('detalles_pedido')->insert([
                    'id_pedido' => $pedido->id_pedido,
                    'id_producto' => $producto->id_producto,
                    'cantidad' => $cantidad,
                    'subtotal' => $producto->precio * $cantidad,
                ]);
            }
        }

        echo "✅ Se insertaron detalles de pedido correctamente.\n";
    }
}
