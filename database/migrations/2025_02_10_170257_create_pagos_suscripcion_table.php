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
        Schema::create('pagos_suscripcion', function (Blueprint $table) {
            $table->integer('id_pago', true); // Auto-incremental
            $table->integer('id_cliente')->index(); // Relación con clientes
            $table->dateTime('fecha_pago'); // Fecha en la que se realizó el pago
            $table->enum('plan_suscripcion', ['mensual', 'anual']); // Tipo de plan
            $table->decimal('monto_pagado', 10, 2); // Monto del pago
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia']); // Método de pago
            $table->string('referencia_pago', 255)->nullable(); // Referencia del pago (opcional)
            $table->enum('estado_pago', ['completado', 'pendiente', 'fallido'])->default('completado'); // Estado del pago
            $table->dateTime('fecha_inicio_suscripcion'); // Fecha de inicio del plan
            $table->dateTime('fecha_fin_suscripcion'); // Fecha de vencimiento del plan
            
            // Relación con la tabla clientes
            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_suscripcion');
    }
};
