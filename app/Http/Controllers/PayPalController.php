<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Usuario;
use App\Models\Cliente;
use App\Models\PagosSuscripcion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PayPalController extends Controller
{
    /**
     * Crear una orden de pago en PayPal
     */
    public function createPayment()
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "amount" => [
                        "currency_code" => "MXN",
                        "value" => "300.00"
                    ]
                ]]
            ]);

            if (isset($response['id']) && $response['id'] != null) {
                foreach ($response['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        return response()->json(['approve_url' => $link['href']]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error("Error en PayPal: " . $e->getMessage());
            return response()->json(['error' => 'Error en la comunicación con PayPal.'], 500);
        }
    }

    /**
     * Capturar el pago después de la aprobación
     */
    public function capturePayment(Request $request)
    {
        if (!$request->has('orderID')) {
            return response()->json(['error' => 'Token de pago no encontrado.'], 400);
        }

        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->capturePaymentOrder($request->input('orderID'));

            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                // Obtener datos del pagador
                $payerEmail = $response['payer']['email_address'];
                $payerName = $response['payer']['name']['given_name'] ?? 'Usuario';

                // Crear o obtener usuario
                $usuario = Usuario::firstOrCreate(
                    ['email' => $payerEmail],
                    [
                        'nombre' => $payerName,
                        'contraseña' => Hash::make(substr(md5(time()), 0, 10)),
                        'rol' => 'cliente_final',
                        'fecha_creacion' => now(),
                    ]
                );

                // Crear o obtener cliente
                $cliente = Cliente::firstOrCreate(
                    ['email_contacto' => $payerEmail],
                    [
                        'id_usuario' => $usuario->id_usuario,
                        'nombre_comercial' => session('nombre_comercial', 'Negocio sin nombre'),
                        'direccion' => session('direccion', 'Sin dirección'),
                        'telefono' => session('telefono', '0000000000'),
                        'plan_suscripcion' => 'mensual', // Plan gestionado desde PayPal
                        'monto_suscripcion' => 300.00, // Establecido a través de PayPal
                        'fecha_registro' => now(),
                        'fecha_fin_suscripcion' => now()->addMonth(),
                        'estado_suscripcion' => 1, // Activado automáticamente al recibir el pago
                        'sector' => session('sector', 'Otros'),
                    ]
                );

                // Crear registro del pago
                PagosSuscripcion::create([
                    'id_cliente' => $cliente->id_cliente,
                    'fecha_pago' => now(),
                    'plan_suscripcion' => 'mensual',
                    'monto_pagado' => 300.00, // Monto especificado
                    'metodo_pago' => 'PayPal',
                    'referencia_pago' => $response['id'] ?? 'N/A',
                    'estado_pago' => 'completado',
                    'fecha_inicio_suscripcion' => now(),
                    'fecha_fin_suscripcion' => now()->addMonth()
                ]);

                // Limpiar la sesión del negocio
                session()->forget(['nombre_comercial', 'direccion', 'telefono', 'sector']);

                return response()->json(['success' => true, 'redirect' => route('dashboard')]);
            }
        } catch (\Exception $e) {
            Log::error("Error en la captura del pago: " . $e->getMessage());
            return response()->json(['error' => 'Error en la captura del pago.'], 500);
        }
    }
}
