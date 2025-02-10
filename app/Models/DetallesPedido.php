<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DetallesPedido
 * 
 * @property int $id_detalle
 * @property int $id_pedido
 * @property int $id_producto
 * @property int $cantidad
 * @property float $subtotal
 * 
 * @property Pedido $pedido
 * @property Producto $producto
 *
 * @package App\Models
 */
class DetallesPedido extends Model
{
	protected $table = 'detalles_pedido';
	protected $primaryKey = 'id_detalle';
	public $timestamps = false;

	protected $casts = [
		'id_pedido' => 'int',
		'id_producto' => 'int',
		'cantidad' => 'int',
		'subtotal' => 'float'
	];

	protected $fillable = [
		'id_pedido',
		'id_producto',
		'cantidad',
		'subtotal'
	];

	public function pedido()
	{
		return $this->belongsTo(Pedido::class, 'id_pedido');
	}

	public function producto()
	{
		return $this->belongsTo(Producto::class, 'id_producto');
	}
}
