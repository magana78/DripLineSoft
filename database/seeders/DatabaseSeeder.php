<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([

            UsuariosClientesSeeder::class,
            SucursalSeeder::class,
            MenuTableSeeder::class,
            ProductoSeeder::class,
            ClienteUsuarioSeeder::class,      
            PedidosTableSeeder::class,
            DetallesPedidoTableSeeder::class, 
            MetodosPagoSeeder::class,
        ]);
    }
}
