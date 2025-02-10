<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Producto
 * 
 * @property int $id_producto
 * @property int $id_menu
 * @property string $nombre_producto
 * @property string|null $descripcion
 * @property float $precio
 * @property bool $disponible
 * 
 * @property Menu $menu
 * @property Collection|DetallesPedido[] $detalles_pedidos
 * @property Collection|ImagenesProducto[] $imagenes_productos
 *
 * @package App\Models
 */
class Producto extends Model
{
	protected $table = 'productos';
	protected $primaryKey = 'id_producto';
	public $timestamps = false;

	protected $casts = [
		'id_menu' => 'int',
		'precio' => 'float',
		'disponible' => 'bool'
	];

	protected $fillable = [
		'id_menu',
		'nombre_producto',
		'descripcion',
		'precio',
		'disponible'
	];

	public function menu()
	{
		return $this->belongsTo(Menu::class, 'id_menu');
	}

	public function detalles_pedidos()
	{
		return $this->hasMany(DetallesPedido::class, 'id_producto');
	}

	public function imagenes_productos()
	{
		return $this->hasMany(ImagenesProducto::class, 'id_producto');
	}
}
