<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Sucursale
 * 
 * @property int $id_sucursal
 * @property int $id_cliente
 * @property string $nombre_sucursal
 * @property string|null $direccion
 * @property string|null $telefono
 * @property string|null $horario_atencion
 * @property int|null $tiempo_entrega_estandar
 * 
 * @property Cliente $cliente
 * @property Collection|Inventario[] $inventarios
 * @property Collection|Menu[] $menus
 * @property Collection|Pedido[] $pedidos
 *
 * @package App\Models
 */
class Sucursale extends Model
{
	protected $table = 'sucursales';
	protected $primaryKey = 'id_sucursal';
	public $timestamps = false;

	protected $casts = [
		'id_cliente' => 'int',
		'tiempo_entrega_estandar' => 'int'
	];

	protected $fillable = [
		'id_cliente',
		'nombre_sucursal',
		'direccion',
		'latitud',  
		'longitud', 
		'telefono',
		'horario_atencion',
		'tiempo_entrega_estandar',
		'activa', 
	];
	

	public function cliente()
	{
		return $this->belongsTo(Cliente::class, 'id_cliente');
	}

	public function inventarios()
	{
		return $this->hasMany(Inventario::class, 'id_sucursal');
	}

	public function menus()
	{
		return $this->hasMany(Menu::class, 'id_sucursal');
	}

	public function pedidos()
	{
		return $this->hasMany(Pedido::class, 'id_sucursal');
	}
}
