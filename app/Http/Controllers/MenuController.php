<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sucursale;
use App\Models\Menu;
use App\Models\Producto;
use App\Models\ImagenesProducto;
use Illuminate\Support\Facades\Auth;  // Importación correcta

class MenuController extends Controller
{

    public function index()
    {
        // Obtener el usuario autenticado
        $usuario = Auth::user();

        // Obtener el cliente asociado al usuario autenticado
        $cliente = $usuario->cliente;

        // Si el usuario no tiene un cliente asociado, redirigir con error
        if (!$cliente) {
            return redirect()->route('dashboard')->with('error', 'No tienes sucursales asociadas.');
        }

        // Obtener todas las sucursales del cliente autenticado
        $sucursales = Sucursale::where('id_cliente', $cliente->id_cliente)->get();

        // Obtener los menús de esas sucursales
        $menus = Menu::whereIn('id_sucursal', $sucursales->pluck('id_sucursal'))
            ->with('sucursale')
            ->get();

        // Retornar la vista con los datos
        return view('menus.index', compact('menus', 'sucursales'));
    }



    /**
     * Muestra el formulario para crear un nuevo menú.
     */
    public function create()
    {
        // Obtener todas las sucursales del cliente autenticado
        $usuario = Auth::user();
        $sucursales = Sucursale::where('id_cliente', $usuario->cliente->id_cliente)->get();

        return view('menus.create', compact('sucursales'));
    }

    /**
     * Guarda el menú en la base de datos.
     */
    public function store(Request $request)
    {
        // Validar los datos
        $request->validate([
            'nombre_menu' => 'required|string|max:255',
            'categoria' => 'required|in:bebidas calientes,bebidas frías,postres,snacks,promociones,ensaladas,entradas,platos fuertes,comida rápida,carnes,mariscos,sopas y caldos,comida mexicana,comida italiana,comida oriental,vegetariano,vegano',
            'id_sucursal' => 'required|exists:sucursales,id_sucursal',
        ]);

        // Guardar el menú
        Menu::create([
            'nombre_menu' => $request->nombre_menu,
            'categoria' => $request->categoria,
            'id_sucursal' => $request->id_sucursal,
        ]);

        return redirect()->route('menus.index')->with('success', 'Menú creado exitosamente.');
    }


    public function show($id)
    {
        // Buscar el menú con sus productos y la sucursal relacionada
        $menu = Menu::with(['productos.imagenes_productos', 'sucursale'])->findOrFail($id);

        // Obtener los productos relacionados con este menú
        $productos = $menu->productos;

        return view('menus.show', compact('menu', 'productos'));
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        // Validar los datos
        $request->validate([
            'nombre_menu' => 'required|string|max:255',
            'categoria' => 'required|in:bebidas calientes,bebidas frías,postres,snacks,promociones,ensaladas,entradas,platos fuertes,comida rápida,carnes,mariscos,sopas y caldos,comida mexicana,comida italiana,comida oriental,vegetariano,vegano',
            'id_sucursal' => 'required|exists:sucursales,id_sucursal',
        ]);


        // Encontrar el menú y actualizarlo
        $menu = Menu::findOrFail($id);
        $menu->update([
            'nombre_menu' => $request->nombre_menu,
            'categoria' => $request->categoria,
            'id_sucursal' => $request->id_sucursal,
        ]);

        return redirect()->route('menus.index')->with('success', 'Menú actualizado correctamente.');
    }

    public function removeProduct($menu_id, $producto_id)
    {
        // Buscar el producto que pertenece a este menú
        $producto = Producto::where('id_producto', $producto_id)
            ->where('id_menu', $menu_id)
            ->first();

        if (!$producto) {
            return redirect()->back()->with('error', 'El producto no está asociado a este menú.');
        }

        // Desvincular el producto del menú (id_menu a NULL)
        $producto->update(['id_menu' => null]);

        return redirect()->route('menus.show', $menu_id)->with('success', 'Producto eliminado del menú exitosamente.');
    }

    public function destroy(string $id)
    {
        //
    }
}
