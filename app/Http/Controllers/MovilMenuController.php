<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Producto;

class MovilMenuController extends Controller
{
    // 📌 Obtener productos de un menú específico
    public function listarProductosPorMenu($idMenu)
    {
        try {
            // ✅ Verificar que el menú exista
            $menu = Menu::find($idMenu);

            if (!$menu) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Menú no encontrado.'
                ], 404);
            }

            // ✅ Obtener productos del menú junto con sus imágenes
            $productos = Producto::where('id_menu', $idMenu)
                                ->with('imagenes_productos') // Relación en el modelo
                                ->get();

            if ($productos->isEmpty()) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'No hay productos disponibles en este menú.'
                ], 404);
            }

            return response()->json([
                'exito' => true,
                'mensaje' => 'Productos obtenidos correctamente.',
                'productos' => $productos
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener los productos: ' . $e->getMessage()
            ], 500);
        }
    }
}
