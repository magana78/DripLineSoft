<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ImagenesProducto
 * 
 * @property int $id_imagen
 * @property int $id_producto
 * @property string $ruta_imagen
 * 
 * @property Producto $producto
 *
 * @package App\Models
 */
class ImagenesProducto extends Model
{
	protected $table = 'imagenes_productos';
	protected $primaryKey = 'id_imagen';
	public $timestamps = false;

	protected $casts = [
		'id_producto' => 'int'
	];

	protected $fillable = [
		'id_producto',
		'ruta_imagen'
	];

	public function producto()
	{
		return $this->belongsTo(Producto::class, 'id_producto');
	}
}
