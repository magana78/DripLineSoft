<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener todos los id_cliente
        $clientes = DB::table('clientes')->pluck('id_cliente');

        // Definir posibles nombres de sucursales
        $nombres_sucursales = [
            'Andares 3', 'Centro', 'Magno', 'Plaza Galerías', 'La Perla', 'Bello Horizonte', 'Torre Uno', 'El Faro', 'Los Arcos', 'La Vista'
        ];

        // Definir direcciones de sucursales (esto puede ir variando también si lo deseas)
        $direcciones = [
            'Avenida Patria, Valentín Gómez Farías, Guadalajara, Región Centro, Jalisco, 44970, México',
            '634, Avenida Patria, 5 de Mayo 1a. Sección, Guadalajara, Región Centro, Jalisco, 44970, México',
            'Plaza Galerías, Avenida Vallarta 5000, Zapopan, Jalisco, 45010, México',
            'Bello Horizonte, 123, Zapopan, Jalisco, 45060, México',
            'Torre Uno, Calle 5 de Febrero 100, Guadalajara, Jalisco, 44450, México'
        ];

        // Asignar sucursales a cada cliente
        foreach ($clientes as $id_cliente) {
            // Determinar cuántas sucursales asignar: 2 o 3
            $num_sucursales = rand(2, 3); // Elige aleatoriamente entre 2 y 3 sucursales
            $sucursales_seleccionadas = array_rand($nombres_sucursales, $num_sucursales);

            // Si se selecciona un solo índice, convertirlo en array
            if (!is_array($sucursales_seleccionadas)) {
                $sucursales_seleccionadas = [$sucursales_seleccionadas];
            }

            // Insertar las sucursales seleccionadas para el cliente
            foreach ($sucursales_seleccionadas as $index) {
                DB::table('sucursales')->insert([
                    'id_cliente' => $id_cliente,
                    'nombre_sucursal' => $nombres_sucursales[$index],  // Seleccionar nombre aleatorio
                    'direccion' => $direcciones[array_rand($direcciones)], // Seleccionar dirección aleatoria
                    'latitud' => 20.6215207,  // Puedes ajustar la latitud y longitud si deseas asignar valores aleatorios o fijos
                    'longitud' => -103.3623643,
                    'telefono' => '+5233333332',  // Aquí puedes también poner teléfonos aleatorios si lo deseas
                    'horario_atencion' => '09:00 - 18:00 (Lunes, Martes, Miércoles, Jueves, Viernes)',
                    'tiempo_entrega_estandar' => null,
                    'activa' => rand(0, 1),  // Randomizar el estado de actividad (0 o 1)
                ]);
            }
        }

        $this->command->info('✅ Seeder de Sucursales completado correctamente.');
    }
}
