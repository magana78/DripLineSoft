<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sucursale;

class MovilSucursalController extends Controller
{
    // 📌 Obtener todas las sucursales
    public function listarSucursales()
    {
        try {
            $sucursales = Sucursale::all();

            if ($sucursales->isEmpty()) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'No hay sucursales disponibles.' // ✅ Siempre devuelve mensaje
                ], 404);
            }

            return response()->json([
                'exito' => true,
                'mensaje' => 'Lista de sucursales obtenida correctamente.', // ✅ Mensaje de éxito
                'sucursales' => $sucursales
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener sucursales: ' . $e->getMessage()
            ], 500);
        }
    }
}
