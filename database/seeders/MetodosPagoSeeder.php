<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Importación de la clase DB

class MetodosPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('metodos_pago')->insert([
            ['nombre_metodo' => 'Efectivo'],
            ['nombre_metodo' => 'Tarjeta de crédito/débito'],
            ['nombre_metodo' => 'Transferencia bancaria'],
        ]);
    }
}
