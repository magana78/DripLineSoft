<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Menu
 * 
 * @property int $id_menu
 * @property int $id_sucursal
 * @property string $nombre_menu
 * @property string $categoria
 * 
 * @property Sucursale $sucursale
 * @property Collection|Producto[] $productos
 *
 * @package App\Models
 */
class Menu extends Model
{
	protected $table = 'menus';
	protected $primaryKey = 'id_menu';
	public $timestamps = false;

	protected $casts = [
		'id_sucursal' => 'int'
	];

	protected $fillable = [
		'id_sucursal',
		'nombre_menu',
		'categoria'
	];

	public function sucursale()
	{
		return $this->belongsTo(Sucursale::class, 'id_sucursal');
	}

	public function productos()
	{
		return $this->hasMany(Producto::class, 'id_menu');
	}
}
