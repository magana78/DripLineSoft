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
        DB::table('sucursales')->insert([
            [
                'id_cliente' => 1,
                'nombre_sucursal' => 'Andares 3',
                'direccion' => 'Avenida Patria, Valentín Gómez Farías, Guadalajara, Región Centro, Jalisco, 44970, México',
                'latitud' => 20.6215207,
                'longitud' => -103.3623643,
                'telefono' => '+5233333332',
                'horario_atencion' => '09:00 - 18:00 (Lunes, Martes, Miércoles, Jueves, Viernes)',
                'tiempo_entrega_estandar' => null,
                'activa' => 0
            ],
            [
                'id_cliente' => 1,
                'nombre_sucursal' => 'Centro',
                'direccion' => '634, Avenida Patria, 5 de Mayo 1a. Sección, Guadalajara, Región Centro, Jalisco, 44970, México',
                'latitud' => 20.6206754,
                'longitud' => -103.3558521,
                'telefono' => '+524545452131',
                'horario_atencion' => '09:11 - 18:59 (Lunes, Martes, Miércoles, Jueves, Viernes, Sábado)',
                'tiempo_entrega_estandar' => null,
                'activa' => 1
            ],
            [
                'id_cliente' => 1,
                'nombre_sucursal' => 'Magno',
                'direccion' => '634, Avenida Patria, 5 de Mayo 1a. Sección, Guadalajara, Región Centro, Jalisco, 44970, México',
                'latitud' => 20.6206754,
                'longitud' => -103.3558521,
                'telefono' => '+521234567891',
                'horario_atencion' => '09:30 - 22:30 (Lunes)',
                'tiempo_entrega_estandar' => null,
                'activa' => 1
            ]
        ]);
    }
}
