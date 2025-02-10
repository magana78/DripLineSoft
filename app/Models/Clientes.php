<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $primaryKey = 'id_cliente';  // Suponiendo que esta es la clave primaria


    protected $fillable = [
        'id_usuario', 'nombre_comercial', 'direccion', 'telefono',
        'email_contacto', 'plan_suscripcion', 'monto_suscripcion',
        'fecha_registro', 'fecha_fin_suscripcion', 'estado_suscripcion', 'sector',
    ];

    // Modelo Cliente
    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }

}
