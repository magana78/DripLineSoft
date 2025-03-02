<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sucursale;
use App\Models\Menu;
use App\Models\Producto;
use App\Models\ImagenesProducto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /**
     * Muestra la lista de productos.
     */
    public function index()
    {
        $usuario = Auth::user();
        $cliente = $usuario->cliente;

        if (!$cliente) {
            return redirect()->route('dashboard')->with('error', 'No tienes sucursales asociadas.');
        }

        $sucursales = Sucursale::where('id_cliente', $cliente->id_cliente)->get();

        // Asegurar que se carga el menú y la sucursal en la consulta
        $productos = Producto::with(['menu.sucursale'])
            ->whereIn('id_menu', Menu::whereIn('id_sucursal', $sucursales->pluck('id_sucursal'))->pluck('id_menu'))
            ->get();

        return view('productos.index', compact('productos', 'sucursales'));
    }


    /**
     * Muestra el formulario para crear un producto.
     */
    public function create()
    {
        $usuario = Auth::user();
        $sucursales = Sucursale::where('id_cliente', $usuario->cliente->id_cliente)->get();

        return view('productos.create', compact('sucursales'));
    }

    /**
     * Guarda un nuevo producto en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_sucursal' => 'required|exists:sucursales,id_sucursal',
            'id_menu' => 'required|exists:menus,id_menu',
            'nombre_producto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'disponible' => 'required|boolean',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $producto = Producto::create([
            'id_menu' => $request->id_menu,
            'nombre_producto' => $request->nombre_producto,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'disponible' => $request->disponible
        ]);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imagen) {
                $path = $imagen->store('productos', 'public');
                ImagenesProducto::create([
                    'id_producto' => $producto->id_producto,
                    'ruta_imagen' => $path
                ]);
            }
        }


        return redirect()->route('productos.index')->with('success', 'Producto creado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        // Validación de datos
        $request->validate([
            'nombre_producto' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'id_sucursal' => 'required|exists:sucursales,id_sucursal',
            'id_menu' => 'required|exists:menus,id_menu',
            'imagenes.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'deleted_images' => 'nullable|string',
        ]);

        $producto = Producto::findOrFail($id);

        // 🔹 **Actualizar datos generales del producto**
        $producto->update([
            'nombre_producto' => $request->nombre_producto,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'id_menu' => $request->id_menu,
        ]);

        // 🔹 **Manejo de imágenes eliminadas**
        if ($request->has('deleted_images')) {
            $deletedImages = explode(',', $request->deleted_images);
            foreach ($deletedImages as $imageId) {
                $imagen = ImagenesProducto::find($imageId);
                if ($imagen) {
                    Storage::disk('public')->delete($imagen->ruta_imagen);  // Eliminar del almacenamiento
                    $imagen->delete();  // Eliminar de la base de datos
                }
            }
        }

        // 🔹 **Manejo de imágenes editadas (precargadas)**
        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $imageId => $newImage) {
                if (is_numeric($imageId)) {  // Si la clave del array es un ID, es una imagen existente
                    $imagen = ImagenesProducto::find($imageId);
                    if ($imagen) {
                        // Eliminar la imagen anterior
                        Storage::disk('public')->delete($imagen->ruta_imagen);

                        // Guardar la nueva imagen
                        $path = $newImage->store('productos', 'public');
                        $imagen->update([
                            'ruta_imagen' => $path,
                        ]);
                    }
                }
            }
        }

        // 🔹 **Manejo de imágenes nuevas (separadas de las editadas)**
        if ($request->hasFile('imagenes_nuevas')) {
            $totalImages = $producto->imagenes_productos()->count();
            $remainingSlots = 4 - $totalImages;

            foreach ($request->file('imagenes_nuevas') as $imagen) {
                if ($remainingSlots <= 0) break;  // Detener si ya se alcanzó el máximo

                $path = $imagen->store('productos', 'public');
                ImagenesProducto::create([
                    'id_producto' => $producto->id_producto,
                    'ruta_imagen' => $path
                ]);

                $remainingSlots--; // Reducir la cantidad de espacios restantes
            }
        }

        return redirect()->route('productos.index')->with('success', 'Producto actualizado exitosamente.');
    }




    /**
     * Muestra los detalles de un producto específico.
     */
    public function show($id)
    {
        $usuario = Auth::user();
        $cliente = $usuario->cliente;

        if (!$cliente) {
            return redirect()->route('dashboard')->with('error', 'No tienes sucursales asociadas.');
        }

        $sucursales = Sucursale::where('id_cliente', $cliente->id_cliente)->get();

        $producto = Producto::with(['imagenes_productos', 'menu.sucursale'])
            ->findOrFail($id);

        return view('productos.show', compact('producto', 'sucursales'));
    }


    /**
     * Obtiene los menús de una sucursal específica.
     */
    public function getMenusBySucursal($id_sucursal)
    {
        $menus = Menu::where('id_sucursal', $id_sucursal)->get();
        return response()->json($menus);
    }

    public function toggleEstado($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->disponible = !$producto->disponible; // Cambia el estado actual (true <-> false)
        $producto->save();

        return redirect()->back()->with('success', 'Estado del producto actualizado correctamente.');
    }
}
