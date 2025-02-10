<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoSuscripcion extends Model
{
    use HasFactory;

    protected $table = 'pagos_suscripcion';

    protected $primaryKey = 'id_pago';

    public $timestamps = false; // Desactivamos timestamps si no tienes columnas created_at y updated_at

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

    /**
     * Relación con la tabla clientes
     */
    public function cliente()
    {
        return $this->belongsTo(Clientes::class, 'id_cliente', 'id_cliente');
    }
}
