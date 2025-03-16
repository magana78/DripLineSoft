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
        // Obtener los IDs de clientes existentes en la tabla 'clientes'
        $clientes = DB::table('clientes')->pluck('id_cliente');

        // Obtener los IDs de usuarios con el rol 'cliente_final'
        $usuariosClientes = DB::table('usuarios')
            ->where('rol', 'cliente_final')
            ->pluck('id_usuario');

        // Verificar que existan datos en ambas tablas
        if ($clientes->isEmpty() || $usuariosClientes->isEmpty()) {
            $this->command->warn('⚠️ No se encontraron clientes o usuarios con el rol "cliente_final".');
            return;
        }

        // Crear combinaciones donde cada cliente_final se asigne a cada admin_cliente
        $clienteUsuarios = [];
        foreach ($clientes as $cliente) {
            foreach ($usuariosClientes as $usuario) {
                $clienteUsuarios[] = [
                    'id_cliente' => $cliente,
                    'id_usuario' => $usuario
                ];
            }
        }

        // Insertar datos en la tabla pivote
        DB::table('cliente_usuario')->insert($clienteUsuarios);

        $this->command->info('✅ Seeder de ClienteUsuario completado correctamente. Se asignaron todos los clientes_final a todos los admin_cliente.');
    }
}
