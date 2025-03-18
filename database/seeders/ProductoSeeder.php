<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run()
    {
        // Obtener todos los menús para asociarlos a los productos
        $menus = DB::table('menus')->get();

        // Crear productos para cada menú
        foreach ($menus as $menu) {
            $productos = $this->getProductosPorCategoria($menu->categoria);

            // Insertar productos para cada menú
            foreach ($productos as $producto) {
                DB::table('productos')->insert([
                    'id_menu' => $menu->id_menu,
                    'nombre_producto' => $producto['nombre'],
                    'descripcion' => $producto['descripcion'],
                    'precio' => $producto['precio'],
                    'disponible' => true, // Todos los productos estarán activos (true)
                ]);
            }
        }

        $this->command->info('✅ Seeder de Productos completado correctamente.');
    }

    // Función para obtener productos según la categoría
    // Función para obtener productos según la categoría
    private function getProductosPorCategoria($categoria)
    {
        $productos = [
            'bebidas calientes' => [
                ['nombre' => 'Café Americano', 'descripcion' => 'Café negro fuerte', 'precio' => 30],
                ['nombre' => 'Café con Leche', 'descripcion' => 'Café con leche espumosa', 'precio' => 35],
                ['nombre' => 'Cappuccino', 'descripcion' => 'Café con leche y espuma', 'precio' => 40],
                ['nombre' => 'Latte', 'descripcion' => 'Café suave con leche', 'precio' => 38],
                ['nombre' => 'Expresso', 'descripcion' => 'Café concentrado', 'precio' => 25],
            ],
            'bebidas frías' => [
                ['nombre' => 'Smoothie de Fresa', 'descripcion' => 'Batido de fresa con yogur', 'precio' => 50],
                ['nombre' => 'Café Frappé', 'descripcion' => 'Café frío con hielo', 'precio' => 45],
                ['nombre' => 'Limonada', 'descripcion' => 'Limonada refrescante', 'precio' => 25],
                ['nombre' => 'Mojito sin alcohol', 'descripcion' => 'Mojito sin ron', 'precio' => 35],
                ['nombre' => 'Jugo de Naranja', 'descripcion' => 'Jugo natural de naranja', 'precio' => 40],
            ],
            'postres' => [
                ['nombre' => 'Brownie', 'descripcion' => 'Brownie de chocolate', 'precio' => 30],
                ['nombre' => 'Cheesecake', 'descripcion' => 'Pastel de queso cremoso', 'precio' => 50],
                ['nombre' => 'Tarta de Limón', 'descripcion' => 'Postre de limón con base de galleta', 'precio' => 40],
                ['nombre' => 'Galletas', 'descripcion' => 'Galletas caseras', 'precio' => 25],
                ['nombre' => 'Panquecillos', 'descripcion' => 'Pequeños panqueques con miel', 'precio' => 20],
            ],
            'snacks' => [
                ['nombre' => 'Croissant', 'descripcion' => 'Pastelito de mantequilla', 'precio' => 30],
                ['nombre' => 'Bocadillo de jamón y queso', 'descripcion' => 'Bocadillo de pan con jamón y queso', 'precio' => 40],
                ['nombre' => 'Empanada', 'descripcion' => 'Empanada rellena de carne o pollo', 'precio' => 35],
                ['nombre' => 'Pan de Chocolate', 'descripcion' => 'Pan con trozos de chocolate', 'precio' => 28],
                ['nombre' => 'Bagel', 'descripcion' => 'Panecillo redondo con semillas de sésamo', 'precio' => 25],
            ],
            'platos fuertes' => [
                ['nombre' => 'Filete de Res', 'descripcion' => 'Filete jugoso a la parrilla', 'precio' => 150],
                ['nombre' => 'Pechuga de Pollo al Grill', 'descripcion' => 'Pechuga de pollo a la parrilla', 'precio' => 120],
                ['nombre' => 'Salmón a la Parrilla', 'descripcion' => 'Salmón fresco a la parrilla', 'precio' => 180],
                ['nombre' => 'Costillas BBQ', 'descripcion' => 'Costillas con salsa barbacoa', 'precio' => 200],
                ['nombre' => 'Lomo de Cerdo', 'descripcion' => 'Lomo de cerdo al horno', 'precio' => 160],
            ],
            'comida mexicana' => [
                ['nombre' => 'Tacos al Pastor', 'descripcion' => 'Tacos de cerdo marinado al pastor', 'precio' => 50],
                ['nombre' => 'Tacos de Carnitas', 'descripcion' => 'Tacos con carne de cerdo frita', 'precio' => 55],
                ['nombre' => 'Burritos', 'descripcion' => 'Burrito relleno de carne y frijoles', 'precio' => 80],
                ['nombre' => 'Enchiladas', 'descripcion' => 'Tortillas rellenas de carne cubiertas de salsa', 'precio' => 85],
                ['nombre' => 'Quesadillas', 'descripcion' => 'Tortillas con queso y diferentes rellenos', 'precio' => 60],
            ],
            'comida italiana' => [
                ['nombre' => 'Pizza Margarita', 'descripcion' => 'Pizza con tomate, queso y albahaca', 'precio' => 120],
                ['nombre' => 'Pasta Alfredo', 'descripcion' => 'Pasta con salsa cremosa de queso', 'precio' => 100],
                ['nombre' => 'Lasagna', 'descripcion' => 'Lasagna con carne y queso', 'precio' => 130],
                ['nombre' => 'Ravioli', 'descripcion' => 'Pasta rellena de queso', 'precio' => 110],
                ['nombre' => 'Focaccia', 'descripcion' => 'Pan italiano con hierbas', 'precio' => 50],
            ],
            'comida oriental' => [
                ['nombre' => 'Sushi', 'descripcion' => 'Sushi con atún y aguacate', 'precio' => 150],
                ['nombre' => 'Ramen', 'descripcion' => 'Sopa japonesa de fideos', 'precio' => 80],
                ['nombre' => 'Dim Sum', 'descripcion' => 'Bocaditos de masa rellenos', 'precio' => 60],
                ['nombre' => 'Arroz Frito', 'descripcion' => 'Arroz con verduras y huevo', 'precio' => 40],
                ['nombre' => 'Pechuga de Pollo a la Naranja', 'descripcion' => 'Pollo con salsa de naranja', 'precio' => 120],
            ],
            'comida rápida' => [
                ['nombre' => 'Hamburguesa con Papas', 'descripcion' => 'Hamburguesa con carne de res y papas fritas', 'precio' => 90],
                ['nombre' => 'Hot Dog', 'descripcion' => 'Perro caliente con pan y mostaza', 'precio' => 50],
                ['nombre' => 'Pizza Fast', 'descripcion' => 'Pizza rápida con ingredientes frescos', 'precio' => 120],
                ['nombre' => 'Fried Chicken', 'descripcion' => 'Pollo frito con salsa secreta', 'precio' => 80],
                ['nombre' => 'Nachos con Queso', 'descripcion' => 'Nachos con queso fundido', 'precio' => 40],
            ],
            'carnes' => [
                ['nombre' => 'T-bone Steak', 'descripcion' => 'Corte de carne T-bone', 'precio' => 250],
                ['nombre' => 'Hamburguesa Gourmet', 'descripcion' => 'Hamburguesa premium con queso cheddar', 'precio' => 120],
                ['nombre' => 'Costillas de Cerdo', 'descripcion' => 'Costillas de cerdo a la barbacoa', 'precio' => 220],
                ['nombre' => 'Entrecot', 'descripcion' => 'Corte de carne entrecot a la parrilla', 'precio' => 200],
                ['nombre' => 'Bife de Chorizo', 'descripcion' => 'Corte argentino de carne', 'precio' => 180],
            ],
            'mariscos' => [
                ['nombre' => 'Camarones al Ajillo', 'descripcion' => 'Camarones cocinados con ajo', 'precio' => 180],
                ['nombre' => 'Tacos de Pescado', 'descripcion' => 'Tacos con filete de pescado fresco', 'precio' => 70],
                ['nombre' => 'Sopa de Mariscos', 'descripcion' => 'Sopa con diversos mariscos', 'precio' => 150],
                ['nombre' => 'Paella', 'descripcion' => 'Paella de mariscos con arroz', 'precio' => 200],
                ['nombre' => 'Pulpo a la Parrilla', 'descripcion' => 'Pulpo a la parrilla con aceite de oliva', 'precio' => 220],
            ],
            'sopas y caldos' => [
                ['nombre' => 'Sopa de Pollo', 'descripcion' => 'Sopa tradicional de pollo', 'precio' => 50],
                ['nombre' => 'Sopa de Mariscos', 'descripcion' => 'Sopa con mariscos frescos', 'precio' => 70],
                ['nombre' => 'Sopa de Tortilla', 'descripcion' => 'Sopa picante de tortilla de maíz', 'precio' => 45],
                ['nombre' => 'Caldo de Res', 'descripcion' => 'Caldo de res con verduras', 'precio' => 80],
                ['nombre' => 'Sopa Miso', 'descripcion' => 'Sopa japonesa de miso', 'precio' => 40],
            ],
            'vegetariano' => [
                ['nombre' => 'Ensalada César', 'descripcion' => 'Ensalada con aderezo césar y crutones', 'precio' => 60],
                ['nombre' => 'Tofu a la Parrilla', 'descripcion' => 'Tofu a la parrilla con salsa de soya', 'precio' => 80],
                ['nombre' => 'Hamburguesa Vegetariana', 'descripcion' => 'Hamburguesa a base de vegetales', 'precio' => 90],
                ['nombre' => 'Lasaña Vegetariana', 'descripcion' => 'Lasaña sin carne, con vegetales y queso', 'precio' => 120],
                ['nombre' => 'Pizza Vegetariana', 'descripcion' => 'Pizza con verduras y queso', 'precio' => 110],
            ],
            'vegano' => [
                ['nombre' => 'Ensalada Vegana', 'descripcion' => 'Ensalada fresca con aguacate', 'precio' => 50],
                ['nombre' => 'Bowl de Quinoa', 'descripcion' => 'Bowl de quinoa con vegetales', 'precio' => 70],
                ['nombre' => 'Tacos Veganos', 'descripcion' => 'Tacos con verduras y salsa vegana', 'precio' => 55],
                ['nombre' => 'Hamburguesa Vegana', 'descripcion' => 'Hamburguesa a base de plantas', 'precio' => 90],
                ['nombre' => 'Smoothie Verde', 'descripcion' => 'Batido verde con espinacas y manzana', 'precio' => 40],
            ],
        ];

        // Devuelve los productos de la categoría especificada
        return isset($productos[$categoria]) ? $productos[$categoria] : [];
    }
}
