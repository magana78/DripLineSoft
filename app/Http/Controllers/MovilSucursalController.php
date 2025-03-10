<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sucursale;
use App\Models\Menu;

class MovilSucursalController extends Controller
{
    // 📌 Obtener menús de una sucursal específica
    public function listarMenusPorSucursal($idSucursal)
    {
        try {
            // ✅ Verificar que la sucursal exista
            $sucursal = Sucursale::find($idSucursal);

            if (!$sucursal) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Sucursal no encontrada.'
                ], 404);
            }

            // ✅ Obtener menús asociados
            $menus = Menu::where('id_sucursal', $idSucursal)
                        ->select('id_menu', 'nombre_menu', 'categoria')
                        ->get();

            if ($menus->isEmpty()) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'No hay menús disponibles para esta sucursal.'
                ], 404);
            }

            return response()->json([
                'exito' => true,
                'mensaje' => 'Menús obtenidos correctamente.',
                'menus' => $menus
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener los menús: ' . $e->getMessage()
            ], 500);
        }
    }
}
