<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  // Importación correcta
use App\Models\Usuario; // Importa el modelo correcto
use App\Models\Sucursale; // Importa el modelo correcto



class SucursalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $sucursales = Sucursale::all(); // Obtener todas las sucursales
    return view('sucursales.index', compact('sucursales'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sucursales.create'); // No necesitas pasar clientes manualmente
    }

    public function store(Request $request)
    {
        $usuario = Auth::user();
    
        // Verificar si hay un usuario autenticado
        if (!$usuario) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para continuar.');
        }
    
        // Verificar si el usuario tiene un cliente asociado
        $cliente = $usuario->cliente;
    
        if (!$cliente) {
            return redirect()->back()->with('error', 'No se encontró un cliente asociado al usuario.');
        }
    
        // Validar los datos del formulario
        $request->validate([
            'nombre_sucursal' => 'required|string|max:255',
            'direccion' => 'nullable|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'region' => 'required|string',
            'telefono' => 'required|digits_between:8,10',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i',
            'dias' => 'required|array',
            'tiempo_entrega_estandar' => 'nullable|integer|min:0',
        ]);

        // Concatenar los días seleccionados
        $diasSeleccionados = implode(', ', $request->dias);

        // Concatenar el horario de atención
        $horarioAtencion = "{$request->hora_inicio} - {$request->hora_fin} ({$diasSeleccionados})";

        // Concatenar prefijo de región con el teléfono
        $telefonoCompleto = $request->region . $request->telefono;

        Sucursale::create([
            'id_cliente' => $cliente->id_cliente,
            'nombre_sucursal' => $request->nombre_sucursal,
            'direccion' => $request->direccion,
            'latitud' => $request->lat,  // Guardar latitud
            'longitud' => $request->lng,  // Guardar longitud
            'telefono' => $telefonoCompleto,
            'horario_atencion' => $horarioAtencion,
            'tiempo_entrega_estandar' => $request->tiempo_entrega_estandar,
        ]);
        
        return redirect()->route('sucursales.index')->with('success', 'Sucursal registrada exitosamente.');
    }
    public function toggleEstado($id)
    {
        $sucursal = Sucursale::findOrFail($id);
        $sucursal->activa = !$sucursal->activa; // Cambia el estado actual (true <-> false)
        $sucursal->save();

        return redirect()->back()->with('success', 'Estado de la sucursal actualizado correctamente.');
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $sucursal = Sucursale::findOrFail($id); // Buscar la sucursal por ID
        return view('sucursales.show', compact('sucursal'));
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
