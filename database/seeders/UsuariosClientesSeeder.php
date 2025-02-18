<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsuariosClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertar usuarios en la tabla 'usuarios'
        $usuarios = [
            [
                'nombre' => 'Bryan De La Torre',
                'email' => 'b@gmail.com',
                'contraseña' => Hash::make('12345678'), // Hashear la contraseña
                'rol' => 'admin_cliente', // Valor válido según la migración
                'fecha_creacion' => now(),
            ],
            [
                'nombre' => 'Juan Pérez',
                'email' => 'juan.perez@example.com',
                'contraseña' => Hash::make('12345678'), // Hashear la contraseña
                'rol' => 'admin_cliente', // Valor válido según la migración
                'fecha_creacion' => now(),
            ],
            [
                'nombre' => 'María López',
                'email' => 'maria.lopez@example.com',
                'contraseña' => Hash::make('12345678'),
                'rol' => 'cliente_final',
                'fecha_creacion' => now(),
            ],
        ];

        foreach ($usuarios as $usuario) {
            $idUsuario = DB::table('usuarios')->insertGetId($usuario);

            // Insertar clientes asociados al usuario creado
            DB::table('clientes')->insert([
                'id_usuario' => $idUsuario,
                'nombre_comercial' => 'Comercial ' . $usuario['nombre'],
                'direccion' => 'Dirección ficticia ' . rand(1, 100),
                'telefono' => '555-' . rand(1000, 9999),
                'email_contacto' => strtolower('contacto.' . $usuario['email']),
                'plan_suscripcion' => rand(0, 1) ? 'mensual' : 'anual',
                'monto_suscripcion' => rand(500, 5000),
                'fecha_registro' => now(),
                'fecha_fin_suscripcion' => now()->addMonths(rand(1, 12)),
                'estado_suscripcion' => 'pendiente',
                'sector' => ['cafetería', 'restaurante', 'otro'][array_rand(['cafetería', 'restaurante', 'otro'])],
            ]);
        }
    }
}
