<?php

namespace App\Mail;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoEntregadoCanceladoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    public $accion;

    public function __construct(Pedido $pedido, $accion)
    {
        $this->pedido = $pedido;
        $this->accion = $accion;
    }

    public function build()
    {
        return $this->subject('Estado de tu pedido')
                    ->view('emails.pedido_entregado_cancelado')
                    ->with([
                        'pedido' => $this->pedido,
                        'accion' => $this->accion,
                    ]);
    }
}

