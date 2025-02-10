<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Inventario
 * 
 * @property int $id_inventario
 * @property int $id_sucursal
 * @property string $nombre_item
 * @property int $cantidad
 * @property string $unidad_medida
 * @property int $umbral_minimo
 * 
 * @property Sucursale $sucursale
 *
 * @package App\Models
 */
class Inventario extends Model
{
	protected $table = 'inventario';
	protected $primaryKey = 'id_inventario';
	public $timestamps = false;

	protected $casts = [
		'id_sucursal' => 'int',
		'cantidad' => 'int',
		'umbral_minimo' => 'int'
	];

	protected $fillable = [
		'id_sucursal',
		'nombre_item',
		'cantidad',
		'unidad_medida',
		'umbral_minimo'
	];

	public function sucursale()
	{
		return $this->belongsTo(Sucursale::class, 'id_sucursal');
	}
}
