<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductosTableSeeder extends Seeder
{
    public function run()
    {
        // Verifica si hay un menú existente para asignar al producto
        $menu = DB::table('menus')->first();

        if (!$menu) {
            echo "⚠️ No se encontraron menús. Agrega datos primero en la tabla 'menus'.\n";
            return;
        }

        // Lista de 5 productos a insertar
        $productos = [
            [
                'nombre_producto' => 'Hamburguesa Clásica',
                'descripcion' => 'Hamburguesa con carne de res, lechuga, tomate y queso cheddar.',
                'precio' => 120.00,
                'disponible' => 1
            ],
            [
                'nombre_producto' => 'Pizza Hawaiana',
                'descripcion' => 'Pizza con piña, jamón y queso mozzarella.',
                'precio' => 150.00,
                'disponible' => 1
            ],
            [
                'nombre_producto' => 'Tacos al Pastor',
                'descripcion' => 'Tacos con carne de cerdo adobada y piña.',
                'precio' => 100.00,
                'disponible' => 1
            ],
            [
                'nombre_producto' => 'Ensalada César',
                'descripcion' => 'Ensalada con pollo a la parrilla, lechuga romana y aderezo César.',
                'precio' => 90.00,
                'disponible' => 1
            ],
            [
                'nombre_producto' => 'Hot Dog Clásico',
                'descripcion' => 'Hot Dog con salchicha de res, cebolla y mayonesa.',
                'precio' => 80.00,
                'disponible' => 1
            ]
        ];

        // Insertar los productos en la base de datos
        foreach ($productos as $producto) {
            DB::table('productos')->insert([
                'id_menu' => $menu->id_menu,
                'nombre_producto' => $producto['nombre_producto'],
                'descripcion' => $producto['descripcion'],
                'precio' => $producto['precio'],
                'disponible' => $producto['disponible'],
            ]);
        }

        echo "✅ Se insertaron 5 productos correctamente.\n";
    }
}
