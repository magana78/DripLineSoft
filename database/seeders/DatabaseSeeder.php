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
            MetodosPagoSeeder::class,
            UsuariosClientesSeeder::class,
            SucursalSeeder::class,
            ClienteUsuarioSeeder::class,      
            DetallesPedidoTableSeeder::class, 
            PedidosTableSeeder::class,
            ProductosTableSeeder::class       
        ]);
    }
}
