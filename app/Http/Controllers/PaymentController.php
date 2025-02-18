<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // Importación correcta
use App\Models\Usuario; // Importa el modelo correcto
use App\Models\Cliente;
use App\Models\ClientesMetodosPago;
use App\Models\MetodosPago;

class PaymentController extends Controller
{
    /**
     * Mostrar la vista de métodos de pago del usuario autenticado.
     */
    public function index()
    {
        $user = Auth::user();
        $cliente = Cliente::where('id_usuario', $user->id_usuario)->first();
        
        if (!$cliente) {
            return redirect()->back()->with('error', 'No se encontró un cliente asociado a este usuario.');
        }

        // Métodos de pago asociados al cliente
        $paymentMethods = $cliente->metodos_pagos()->get();
        
        // Todos los métodos de pago disponibles
        $allPaymentMethods = MetodosPago::all();
        
        // IDs de los métodos de pago ya seleccionados por el cliente
        $userPaymentMethods = $paymentMethods->pluck('id_metodo_pago')->toArray();

        return view('payments.index', compact('paymentMethods', 'allPaymentMethods', 'userPaymentMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Guardar los métodos de pago seleccionados por el usuario.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $cliente = Cliente::where('id_usuario', $user->id_usuario)->first();
        
        if (!$cliente) {
            return redirect()->back()->with('error', 'No se encontró un cliente asociado a este usuario.');
        }

        // Obtener los métodos de pago seleccionados
        $selectedMethods = $request->input('methods', []);

        // Eliminar los métodos de pago actuales del cliente
        ClientesMetodosPago::where('id_cliente', $cliente->id_cliente)->delete();

        // Asociar los métodos de pago seleccionados
        foreach ($selectedMethods as $methodId) {
            ClientesMetodosPago::create([
                'id_cliente' => $cliente->id_cliente,
                'id_metodo_pago' => $methodId
            ]);
        }

        return redirect()->route('payments.index')->with('success', 'Métodos de pago actualizados correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
