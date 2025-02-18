<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        
        $this->call(MetodosPagoSeeder::class);
        $this->call(UsuariosClientesSeeder::class);
        $this->call(SucursalSeeder::class);



    }

     // Llamar al seeder de métodos de pago
}
