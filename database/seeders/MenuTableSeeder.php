<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuTableSeeder extends Seeder
{
    public function run()
    {
        // Obtener todos los clientes
        $clientes = DB::table('clientes')->get();

        // Definir los menús para cada sector
        $menu_cafeteria = [
            'bebidas calientes' => ['Café Americano', 'Café con Leche', 'Cappuccino', 'Latte', 'Expresso'],
            'bebidas frías' => ['Smoothie de Fresa', 'Café Frappé', 'Limonada', 'Mojito sin alcohol'],
            'postres' => ['Brownie', 'Cheesecake', 'Tarta de Limón', 'Galletas', 'Panquecillos'],
            'snacks' => ['Croissant', 'Bocadillo de jamón y queso', 'Empanada', 'Pan de Chocolate'],
        ];

        $menu_restaurante = [
            'platos fuertes' => ['Filete de Res', 'Pechuga de Pollo al Grill', 'Salmón a la Parrilla', 'Costillas BBQ', 'Lomo de Cerdo'],
            'comida mexicana' => ['Tacos al Pastor', 'Tacos de Carnitas', 'Burritos', 'Enchiladas', 'Quesadillas'],
            'comida italiana' => ['Pizza Margarita', 'Pasta Alfredo', 'Lasagna', 'Ravioli', 'Focaccia'],
            'comida oriental' => ['Sushi', 'Ramen', 'Dim Sum', 'Arroz Frito', 'Pechuga de Pollo a la Naranja'],
            'carnes' => ['T-bone Steak', 'Hamburguesa con Papas', 'Costillas a la Barbacoa', 'Entrecot'],
            'mariscos' => ['Camarones al Ajillo', 'Tacos de Pescado', 'Sopa de Mariscos', 'Paella'],
            'sopas y caldos' => ['Sopa de Pollo', 'Sopa de Mariscos', 'Sopa de Tortilla', 'Caldo de Res'],
        ];

        // Iterar sobre cada cliente para asignar un menú
        foreach ($clientes as $cliente) {
            // Obtener las sucursales de este cliente
            $sucursales = DB::table('sucursales')->where('id_cliente', $cliente->id_cliente)->get();

            foreach ($sucursales as $sucursal) {
                // Según el sector del cliente, asignar los menús correspondientes
                $sector = $cliente->sector;

                // Seleccionar un menú según el sector
                if ($sector === 'cafetería') {
                    // Para cafetería, asignar los menús de cafetería
                    $categorias = array_keys($menu_cafeteria);
                    foreach ($categorias as $categoria) {
                        foreach ($menu_cafeteria[$categoria] as $nombre) {
                            DB::table('menus')->insert([
                                'id_sucursal' => $sucursal->id_sucursal,
                                'nombre_menu' => $nombre,
                                'categoria' => $categoria,
                            ]);
                        }
                    }
                } elseif ($sector === 'restaurante') {
                    // Para restaurante, asignar los menús de restaurante
                    $categorias = array_keys($menu_restaurante);
                    foreach ($categorias as $categoria) {
                        foreach ($menu_restaurante[$categoria] as $nombre) {
                            DB::table('menus')->insert([
                                'id_sucursal' => $sucursal->id_sucursal,
                                'nombre_menu' => $nombre,
                                'categoria' => $categoria,
                            ]);
                        }
                    }
                }
            }
        }

        $this->command->info('✅ Seeder de Menú completado correctamente.');
    }
}
