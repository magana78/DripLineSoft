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
                'contraseña' => Hash::make('12345678'),
                'rol' => 'admin_cliente',
                'fecha_creacion' => now(),
            ],
            [
                'nombre' => 'Juan Pérez',
                'email' => 'juan.perez@example.com',
                'contraseña' => Hash::make('12345678'),
                'rol' => 'admin_cliente',
                'fecha_creacion' => now(),
            ],
            [
                'nombre' => 'María López',
                'email' => 'maria.lopez@example.com',
                'contraseña' => Hash::make('12345678'),
                'rol' => 'cliente_final',
                'fecha_creacion' => now(),
            ],
            [
                'nombre' => 'Carlos Fernández',
                'email' => 'carlos.fernandez@example.com',
                'contraseña' => Hash::make('87654321'),
                'rol' => 'cliente_final',
                'fecha_creacion' => now(),
            ],
            [
                'nombre' => 'Laura González',
                'email' => 'laura.gonzalez@example.com',
                'contraseña' => Hash::make('87654321'),
                'rol' => 'admin_cliente',
                'fecha_creacion' => now(),
            ],
            [
                'nombre' => 'Ana Martínez',
                'email' => 'ana.martinez@example.com',
                'contraseña' => Hash::make('password123'),
                'rol' => 'cliente_final',
                'fecha_creacion' => now(),
            ],
            [
                'nombre' => 'Pedro Ramírez',
                'email' => 'pedro.ramirez@example.com',
                'contraseña' => Hash::make('password123'),
                'rol' => 'admin_cliente',
                'fecha_creacion' => now(),
            ],
            [
                'nombre' => 'Marta Torres',
                'email' => 'marta.torres@example.com',
                'contraseña' => Hash::make('password123'),
                'rol' => 'cliente_final',
                'fecha_creacion' => now(),
            ],
            [
                'nombre' => 'Diego Herrera',
                'email' => 'diego.herrera@example.com',
                'contraseña' => Hash::make('123123123'),
                'rol' => 'cliente_final',
                'fecha_creacion' => now(),
            ],
            [
                'nombre' => 'Elena Cruz',
                'email' => 'elena.cruz@example.com',
                'contraseña' => Hash::make('123123123'),
                'rol' => 'admin_cliente',
                'fecha_creacion' => now(),
            ],
            [
                'nombre' => 'Jorge Ortega',
                'email' => 'jorge.ortega@example.com',
                'contraseña' => Hash::make('password321'),
                'rol' => 'cliente_final',
                'fecha_creacion' => now(),
            ],
            [
                'nombre' => 'Lucía Navarro',
                'email' => 'lucia.navarro@example.com',
                'contraseña' => Hash::make('password321'),
                'rol' => 'cliente_final',
                'fecha_creacion' => now(),
            ],
        ];

        $sectores = ['cafetería', 'restaurante', 'otro'];

        foreach ($usuarios as $usuario) {
            $idUsuario = DB::table('usuarios')->insertGetId($usuario);

            // Insertar solo los usuarios con el rol 'admin_cliente' en la tabla 'clientes'
            if ($usuario['rol'] === 'admin_cliente') {
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
                    'estado_suscripcion' => 'activa',
                    'sector' => $sectores[array_rand($sectores)],
                ]);
            }
        }

        $this->command->info('✅ Seeder de Usuarios y Clientes completado correctamente. Se insertaron solo los admin_cliente en la tabla de clientes.');
    }
}
