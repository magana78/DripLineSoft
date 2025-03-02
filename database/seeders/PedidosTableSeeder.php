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
        // Verifica si hay al menos un usuario y una sucursal en la base de datos
        $usuario = DB::table('usuarios')->first();
        $sucursal = DB::table('sucursales')->first();

        if (!$usuario || !$sucursal) {
            echo "⚠️ No se encontraron usuarios o sucursales. Agrega datos primero.\n";
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
                'fecha_pedido' => Carbon::now()->subMinutes(rand(10, 500)), // Fechas recientes aleatorias
                'metodo_pago' => $metodos_pago[array_rand($metodos_pago)],
                'estado' => $estados[array_rand($estados)], // Se selecciona un estado válido
                'total' => rand(100, 500),
                'descuento' => rand(5, 50),
                'nota' => 'Pedido de prueba ' . $i,
                'tiempo_entrega_estimado' => rand(15, 60), // Tiempo entre 15 y 60 minutos
            ]);
        }

        echo "✅ Se insertaron 5 pedidos correctamente.\n";
    }
}
