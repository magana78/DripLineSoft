<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodosPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertar los métodos de pago
        DB::table('metodos_pago')->insert([
            ['nombre_metodo' => 'Efectivo'],
            ['nombre_metodo' => 'Tarjeta de crédito/débito'],
            ['nombre_metodo' => 'Transferencia bancaria'],
        ]);

        // Obtener todos los clientes
        $clientes = DB::table('clientes')->pluck('id_cliente');  // Obtén todos los IDs de los clientes

        // Obtener todos los métodos de pago
        $metodosPago = DB::table('metodos_pago')->pluck('id_metodo_pago');  // Obtén todos los IDs de los métodos de pago

        // Asociar todos los clientes con todos los métodos de pago
        foreach ($clientes as $idCliente) {
            foreach ($metodosPago as $idMetodoPago) {
                DB::table('clientes_metodos_pago')->insert([
                    'id_cliente' => $idCliente,
                    'id_metodo_pago' => $idMetodoPago
                ]);
            }
        }

        $this->command->info('✅ Seeder de Métodos de Pago completado correctamente.');
    }
}
