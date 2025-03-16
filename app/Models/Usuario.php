<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

/**
 * Class Usuario
 * 
 * @property int $id_usuario
 * @property string $nombre
 * @property string $email
 * @property string $contraseña
 * @property string $rol
 * @property Carbon $fecha_creacion
 * 
 * @property Collection|Cliente[] $clientes
 * @property Collection|Pedido[] $pedidos
 *
 * @package App\Models
 */
class Usuario extends Authenticatable
{

	use Notifiable; // ✔️ Agrega este trait para manejar notificaciones y más
	
	protected $table = 'usuarios';
	protected $primaryKey = 'id_usuario';
	public $timestamps = false;

	protected $casts = [
		'fecha_creacion' => 'datetime'
	];

	protected $fillable = [
		'nombre',
		'email',
		'contraseña',
		'rol',
		'fecha_creacion'
	];

	// Ocultar el campo de contraseña al serializar el modelo
    protected $hidden = [
        'contraseña', 'remember_token',
    ];

	/**
     * Especificar que la contraseña está en la columna 'contraseña' en la base de datos.
     */
    public function getAuthPassword()
    {
        return $this->contraseña;
    }

	public function clientes()
	{
		return $this->hasMany(Cliente::class, 'id_usuario');
	}

	// Para traer un solo id para el metodo de creacion de sucursales
	public function cliente()
	{
		return $this->hasOne(Cliente::class, 'id_usuario', 'id_usuario');
	}
	

	public function pedidos()
	{
		return $this->hasMany(Pedido::class, 'id_usuario_cliente');
	}

	
}
