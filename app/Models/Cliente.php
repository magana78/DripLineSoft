<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cliente
 * 
 * @property int $id_cliente
 * @property int $id_usuario
 * @property string $nombre_comercial
 * @property string $direccion
 * @property string|null $telefono
 * @property string|null $email_contacto
 * @property string $plan_suscripcion
 * @property float $monto_suscripcion
 * @property Carbon $fecha_registro
 * @property Carbon $fecha_fin_suscripcion
 * @property bool $estado_suscripcion
 * @property string|null $sector
 * 
 * @property Usuario $usuario
 * @property Collection|MetodosPago[] $metodos_pagos
 * @property Collection|PagosSuscripcion[] $pagos_suscripcions
 * @property Collection|Sucursale[] $sucursales
 *
 * @package App\Models
 */
class Cliente extends Model
{
	protected $table = 'clientes';
	protected $primaryKey = 'id_cliente';
	public $timestamps = false;

	protected $casts = [
		'id_usuario' => 'int',
		'monto_suscripcion' => 'float',
		'fecha_registro' => 'datetime',
		'fecha_fin_suscripcion' => 'datetime',
		'estado_suscripcion' => 'string'
	];

	protected $fillable = [
		'id_usuario',
		'nombre_comercial',
		'direccion',
		'telefono',
		'email_contacto',
		'plan_suscripcion',
		'monto_suscripcion',
		'fecha_registro',
		'fecha_fin_suscripcion',
		'estado_suscripcion',
		'sector'
	];

	public function usuario()
	{
		return $this->belongsTo(Usuario::class, 'id_usuario');
	}

	public function metodos_pagos()
	{
		return $this->belongsToMany(MetodosPago::class, 'clientes_metodos_pago', 'id_cliente', 'id_metodo_pago')
					->withPivot('id_cliente_metodo_pago');
	}

	public function pagos_suscripcions()
	{
		return $this->hasMany(PagosSuscripcion::class, 'id_cliente');
	}

	public function sucursales()
	{
		return $this->hasMany(Sucursale::class, 'id_cliente');
	}
}
