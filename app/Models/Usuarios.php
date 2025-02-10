<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;



class Usuarios extends Authenticatable
{
    use HasFactory, Notifiable;

    // Especificamos que la tabla de la base de datos es 'usuarios'
    protected $table = 'usuarios';  
    protected $primaryKey = 'id_usuario';  // Cambia a tu clave primaria


    // Laravel espera por defecto los timestamps (created_at, updated_at), si no los tienes en tu tabla, desactívalos
    public $timestamps = false;

    // Definir los campos que se pueden llenar mediante asignación masiva
    protected $fillable = [
        'nombre', 'email', 'contraseña', 'rol', 'fecha_creacion'
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

    /**
     * Relación con la tabla Clientes (un usuario puede tener muchos clientes)
     */
    public function clientes()
    {
        return $this->hasMany(Clientes::class, 'id_usuario', 'id_usuario');  // Clave foránea y primaria correctas
    }
}

