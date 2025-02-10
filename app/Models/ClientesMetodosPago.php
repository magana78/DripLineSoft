<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ClientesMetodosPago
 * 
 * @property int $id_cliente_metodo_pago
 * @property int $id_cliente
 * @property int $id_metodo_pago
 * 
 * @property Cliente $cliente
 * @property MetodosPago $metodos_pago
 *
 * @package App\Models
 */
class ClientesMetodosPago extends Model
{
	protected $table = 'clientes_metodos_pago';
	protected $primaryKey = 'id_cliente_metodo_pago';
	public $timestamps = false;

	protected $casts = [
		'id_cliente' => 'int',
		'id_metodo_pago' => 'int'
	];

	protected $fillable = [
		'id_cliente',
		'id_metodo_pago'
	];

	public function cliente()
	{
		return $this->belongsTo(Cliente::class, 'id_cliente');
	}

	public function metodos_pago()
	{
		return $this->belongsTo(MetodosPago::class, 'id_metodo_pago');
	}
}
