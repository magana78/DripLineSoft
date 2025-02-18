<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;

class PayPalController extends Controller
{
    /**
     * Obtener token de autenticación de PayPal
     */
    private function getAccessToken()
    {
        try {
            Log::info("📌 Obteniendo token de PayPal...");
    
            $clientId = config('services.paypal.client_id');
            $clientSecret = config('services.paypal.secret');
            $apiUrl = config('services.paypal.api_url') . "/v1/oauth2/token";
    
            if (!$clientId || !$clientSecret || !$apiUrl) {
                Log::error("❌ ERROR: PAYPAL_CLIENT_ID, PAYPAL_CLIENT_SECRET o PAYPAL_API_URL no están configurados.");
                return null;
            }
    
            Log::info("📌 Enviando solicitud de token a: $apiUrl");
    
            $response = Http::asForm()
                ->withOptions(['verify' => true])
                ->withBasicAuth($clientId, $clientSecret)
                ->post($apiUrl, ['grant_type' => 'client_credentials']);
    
            if ($response->failed()) {
                Log::error("❌ Error en respuesta de PayPal: " . $response->body());
                return null;
            }
    
            $token = $response->json('access_token');
    
            if (!$token) {
                Log::error("❌ No se recibió un token válido.");
                return null;
            }
    
            Log::info("✅ Token obtenido correctamente.");
            return $token;
        } catch (\Exception $e) {
            Log::error("❌ Excepción en getAccessToken: " . $e->getMessage());
            return null;
        }
    }
    

    /**
     * Crear una orden de pago en PayPal
     */
    public function createOrder(Request $request)
    {
        try {
            Log::info("📌 Iniciando creación de orden de pago...");
    
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return response()->json(['error' => 'No se pudo obtener el token de PayPal'], 500);
            }
    
            // Obtener el usuario basado en el email de contacto registrado
            $cliente = Cliente::where('email_contacto', $request->input('email_contacto'))->first();
            if (!$cliente) {
                return response()->json(['error' => 'Cliente no encontrado'], 404);
            }
    
            $apiUrl = config('services.paypal.api_url') . "/v2/checkout/orders";
    
            Log::info("📌 Enviando solicitud de orden a PayPal...");
    
            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'MXN',
                        'value' => '300.00'
                    ]
                ]],
                'payer' => [
                    'email_address' => $cliente->email_contacto
                ]
            ];
    
            // Enviar la solicitud a PayPal
            $response = Http::withToken($accessToken)
                ->withOptions(['verify' => true])
                ->post($apiUrl, $orderData);
    
            if ($response->failed()) {
                Log::error("❌ Error al crear la orden en PayPal: " . $response->body());
                return response()->json(['error' => 'Error al crear la orden'], 500);
            }
    
            $orderResponse = $response->json();
            $approvalUrl = collect($orderResponse['links'])->firstWhere('rel', 'approve')['href'] ?? null;
    
            if (!$approvalUrl) {
                Log::error("❌ No se encontró el enlace de aprobación en la respuesta de PayPal.");
                return response()->json(['error' => 'No se encontró el enlace de aprobación'], 500);
            }
    
            Log::info("✅ Orden creada con éxito. URL de aprobación: " . $approvalUrl);
    
            return response()->json([
                'success' => true,
                'approval_url' => $approvalUrl,
                'order_id' => $orderResponse['id']
            ]);
        } catch (\Exception $e) {
            Log::error("❌ Excepción en createOrder: " . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }
    
    

    /**
     * Capturar el pago de PayPal
     */
    public function captureOrder(Request $request)
{
    try {
        Log::info("📌 Iniciando captura de pago...");

        $orderID = $request->input('orderID');
        $emailContacto = $request->input('email_contacto');

        Log::info("📌 Datos recibidos -> orderID: $orderID, email_contacto: $emailContacto");

        if (!$orderID || !$emailContacto) {
            Log::error("❌ Falta el orderID o el email del cliente.");
            return response()->json(['error' => 'Falta el orderID o el email del cliente'], 400);
        }

        // Verificar si el email existe en la BD
        $cliente = Cliente::where('email_contacto', $emailContacto)->first();

        if (!$cliente) {
            Log::error("❌ Cliente no encontrado en la base de datos.");
            return response()->json(['error' => 'Cliente no registrado'], 404);
        }

        Log::info("📌 Cliente encontrado en la BD. Actualizando...");

        // Iniciar transacción para actualizar suscripción y registrar pago
        DB::beginTransaction();
        try {
            // Actualizar la suscripción del cliente
            $cliente->update([
                'monto_suscripcion' => 300.00,  
                'estado_suscripcion' => 'activa',    
                'fecha_fin_suscripcion' => now()->addMonth()
            ]);

            // Guardar en la tabla `pagos_suscripcion` (sin `created_at` y `updated_at`)
            DB::table('pagos_suscripcion')->insert([
                'id_cliente' => $cliente->id_cliente,
                'fecha_pago' => now(),
                'plan_suscripcion' => $cliente->plan_suscripcion,
                'monto_pagado' => 300.00,
                'metodo_pago' => 'tarjeta', // 🔹 Asegúrate de que el valor esté en los permitidos ('efectivo', 'tarjeta', 'transferencia')
                'referencia_pago' => $orderID, 
                'estado_pago' => 'completado',
                'fecha_inicio_suscripcion' => now(),
                'fecha_fin_suscripcion' => now()->addMonth()
            ]);

            DB::commit();
            Log::info("✅ Suscripción activada y pago registrado en `pagos_suscripcion`.");

            return response()->json([
                'success' => true,
                'message' => 'Pago completado y suscripción activada',
                'monto_pagado' => 300.00
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("❌ Error al actualizar la base de datos: " . $e->getMessage());
            return response()->json(['error' => 'Error al guardar los datos en la base de datos'], 500);
        }
    } catch (\Exception $e) {
        Log::error("❌ Excepción en captureOrder: " . $e->getMessage());
        return response()->json(['error' => 'Error interno del servidor'], 500);
    }
}

    
    
    

}
