<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PagosSuscripcion
 * 
 * @property int $id_pago
 * @property int $id_cliente
 * @property Carbon $fecha_pago
 * @property string $plan_suscripcion
 * @property float $monto_pagado
 * @property string $metodo_pago
 * @property string|null $referencia_pago
 * @property string $estado_pago
 * @property Carbon $fecha_inicio_suscripcion
 * @property Carbon $fecha_fin_suscripcion
 * 
 * @property Cliente $cliente
 *
 * @package App\Models
 */
class PagosSuscripcion extends Model
{
	protected $table = 'pagos_suscripcion';
	protected $primaryKey = 'id_pago';
	public $timestamps = false;

	protected $casts = [
		'id_cliente' => 'int',
		'fecha_pago' => 'datetime',
		'monto_pagado' => 'float',
		'fecha_inicio_suscripcion' => 'datetime',
		'fecha_fin_suscripcion' => 'datetime'
	];

	protected $fillable = [
		'id_cliente',
		'fecha_pago',
		'plan_suscripcion',
		'monto_pagado',
		'metodo_pago',
		'referencia_pago',
		'estado_pago',
		'fecha_inicio_suscripcion',
		'fecha_fin_suscripcion'
	];

	public function cliente()
	{
		return $this->belongsTo(Cliente::class, 'id_cliente');
	}
}
