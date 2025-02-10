<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->integer('id_pedido', true);
            $table->integer('id_sucursal')->index('id_sucursal');
            $table->integer('id_usuario_cliente')->index('id_usuario_cliente');
            $table->dateTime('fecha_pedido');
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia']);
            $table->enum('estado', ['pendiente', 'en preparación', 'listo', 'cancelado']);
            $table->decimal('total', 10);
            $table->decimal('descuento', 10)->nullable()->default(0);
            $table->text('nota')->nullable();
            $table->integer('tiempo_entrega_estimado')->nullable()->comment('Tiempo estimado de entrega para este pedido (en minutos)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
