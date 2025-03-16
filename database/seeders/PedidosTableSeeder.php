<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PedidosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Verifica si hay al menos un usuario con el rol "cliente_final" y una sucursal en la base de datos
        $usuario = DB::table('usuarios')->where('rol', 'cliente_final')->first();
        $sucursal = DB::table('sucursales')->first();

        if (!$usuario || !$sucursal) {
            echo "⚠️ No se encontraron usuarios con el rol 'cliente_final' o sucursales. Agrega datos primero.\n";
            return;
        }

        // Lista de estados válidos (según el ENUM de la base de datos)
        $estados = ['pendiente', 'en preparación', 'listo', 'cancelado'];

        // Métodos de pago disponibles según el ENUM
        $metodos_pago = ['efectivo', 'tarjeta', 'transferencia'];

        // Insertar 5 pedidos con datos aleatorios
        for ($i = 1; $i <= 5; $i++) {
            DB::table('pedidos')->insert([
                'id_sucursal' => $sucursal->id_sucursal,
                'id_usuario_cliente' => $usuario->id_usuario,
                'fecha_pedido' => Carbon::now()->subMinutes(rand(10, 500)),
                'fecha_entregado' => (rand(0, 1)) ? Carbon::now() : null, // Agregar fecha de entrega aleatoria
                'metodo_pago' => $metodos_pago[array_rand($metodos_pago)],
                'estado' => $estados[array_rand($estados)],
                'total' => rand(100, 500),
                'descuento' => rand(5, 50),
                'nota' => 'Pedido de prueba ' . $i,
                'tiempo_entrega_estimado' => rand(15, 60),
            ]);
        }

        echo "✅ Se insertaron 5 pedidos correctamente para usuarios con el rol 'cliente_final'.\n";
    }
}
