<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Pedido
 * 
 * @property int $id_pedido
 * @property int $id_sucursal
 * @property int $id_usuario_cliente
 * @property Carbon $fecha_pedido
 * @property Carbon|null $fecha_entregado
 * @property string $metodo_pago
 * @property string $estado
 * @property float $total
 * @property float|null $descuento
 * @property string|null $nota
 * @property int|null $tiempo_entrega_estimado
 * 
 * @property Sucursale $sucursale
 * @property Usuario $usuario
 * @property Collection|DetallesPedido[] $detalles_pedidos
 *
 * @package App\Models
 */
class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $primaryKey = 'id_pedido';
    public $timestamps = false;

    protected $casts = [
        'id_sucursal' => 'int',
        'id_usuario_cliente' => 'int',
        'fecha_pedido' => 'datetime',
        'fecha_entregado' => 'datetime', // ➡️ Campo agregado para formatear correctamente la fecha de entrega
        'total' => 'float',
        'descuento' => 'float',
        'tiempo_entrega_estimado' => 'int'
    ];

    protected $fillable = [
        'id_sucursal',
        'id_usuario_cliente',
        'fecha_pedido',
        'fecha_entregado', // ➡️ Campo agregado para poder rellenar este valor al crear registros
        'metodo_pago',
        'estado',
        'total',
        'descuento',
        'nota',
        'tiempo_entrega_estimado'
    ];

    public function sucursale()
    {
        return $this->belongsTo(Sucursale::class, 'id_sucursal');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_cliente');
    }

    public function detalles_pedidos()
    {
        return $this->hasMany(DetallesPedido::class, 'id_pedido');
    }
    // En app/Models/Pedido.php
public function cliente()
{
    return $this->belongsTo(\App\Models\Cliente::class, 'id_usuario_cliente', 'id_cliente');
}

}
