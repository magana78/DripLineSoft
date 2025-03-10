<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Sucursale;
use Illuminate\Support\Facades\Log; // ✅ Importar el log

class MovilClienteController extends Controller
{
    // 📌 Listar todas las sucursales disponibles para clientes
    public function listarSucursalesCliente($idCliente)
    {
        Log::info("🔍 ID del cliente recibido: " . $idCliente);

        try {
            // ✅ Validar que el ID del cliente sea un número
            if (!is_numeric($idCliente)) {
                Log::warning("⚠️ El ID del cliente no es válido: $idCliente");
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'ID de cliente no válido.'
                ], 400); // Código 400 (Solicitud incorrecta)
            }

            // ✅ Buscar cliente como INT para evitar errores
            $cliente = Cliente::where('id_cliente', (int)$idCliente)->first();

            if (!$cliente) {
                Log::warning("⚠️ Cliente con ID $idCliente no encontrado.");
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Cliente no encontrado.'
                ], 404);
            }

            // ✅ Obtener sucursales asociadas al cliente
            $sucursales = Sucursale::where('id_cliente', (int)$idCliente)
                                    ->select('id_sucursal', 'nombre_sucursal', 'direccion', 'telefono')
                                    ->get();

            if ($sucursales->isEmpty()) {
                Log::info("ℹ️ No hay sucursales para el cliente con ID $idCliente.");
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'No hay sucursales disponibles para este cliente.'
                ], 404);
            }

            Log::info("✅ Sucursales encontradas correctamente para el cliente con ID $idCliente.");
            return response()->json([
                'exito' => true,
                'mensaje' => 'Sucursales obtenidas correctamente.',
                'sucursales' => $sucursales
            ], 200);

        } catch (\Exception $e) {
            Log::error("❌ Error en el servidor: " . $e->getMessage());
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener las sucursales: ' . $e->getMessage()
            ], 500);
        }
    }
}
