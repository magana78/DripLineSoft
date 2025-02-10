<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MetodosPago
 * 
 * @property int $id_metodo_pago
 * @property string $nombre_metodo
 * 
 * @property Collection|Cliente[] $clientes
 *
 * @package App\Models
 */
class MetodosPago extends Model
{
	protected $table = 'metodos_pago';
	protected $primaryKey = 'id_metodo_pago';
	public $timestamps = false;

	protected $fillable = [
		'nombre_metodo'
	];

	public function clientes()
	{
		return $this->belongsToMany(Cliente::class, 'clientes_metodos_pago', 'id_metodo_pago', 'id_cliente')
					->withPivot('id_cliente_metodo_pago');
	}
}
