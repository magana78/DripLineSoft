<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Datos ficticios para poblar la tabla
        $clienteUsuarios = [
            ['id_cliente' => 1, 'id_usuario' => 2],
            ['id_cliente' => 1, 'id_usuario' => 3],
            ['id_cliente' => 2, 'id_usuario' => 4],
            ['id_cliente' => 2, 'id_usuario' => 5],
            ['id_cliente' => 3, 'id_usuario' => 6],
        ];

        // Insertar datos en la tabla pivote
        foreach ($clienteUsuarios as $entry) {
            DB::table('cliente_usuario')->insert($entry);
        }

        // Mensaje de confirmación en consola
        $this->command->info('Seeder de ClienteUsuario completado correctamente.');
    }
}
