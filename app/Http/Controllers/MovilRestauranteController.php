<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sucursale; // ✅ Modelo corregido

class MovilRestauranteController extends Controller
{
    // 📌 Obtener todas las sucursales (restaurantes)
    public function listarRestaurantes()
    {
        try {
            $restaurantes = Sucursale::select('id_sucursal', 'nombre_sucursal', 'direccion', 'telefono')->get();

            return response()->json([
                'exito' => true,
                'mensaje' => 'Lista de restaurantes obtenida correctamente.', // ✅ Agregamos mensaje
                'restaurantes' => $restaurantes 
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener restaurantes: ' . $e->getMessage()
            ], 500);
        }
    }

    // 📌 Obtener los detalles de un restaurante por ID
    public function obtenerRestaurante($id)
    {
        try {
            $restaurante = Sucursale::where('id_sucursal', $id)->first();
    
            if (!$restaurante) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Restaurante no encontrado' // ✅ Laravel siempre debe enviar `mensaje`
                ], 404);
            }
    
            return response()->json([
                'exito' => true,
                'mensaje' => 'Restaurante encontrado correctamente.', // ✅ Mensaje agregado
                'restaurante' => $restaurante
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error en el servidor: ' . $e->getMessage()
            ], 500);
        }
    }
}
