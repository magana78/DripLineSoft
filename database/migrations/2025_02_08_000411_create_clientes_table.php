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
        Schema::create('clientes', function (Blueprint $table) {
            $table->integer('id_cliente', true);
            $table->integer('id_usuario')->index('id_usuario');
            $table->string('nombre_comercial');
            $table->text('direccion');
            $table->string('telefono', 20)->nullable();
            $table->string('email_contacto')->nullable()->unique('email_contacto');
            $table->enum('plan_suscripcion', ['mensual', 'anual']);
            $table->decimal('monto_suscripcion', 10);
            $table->dateTime('fecha_registro');
            $table->dateTime('fecha_fin_suscripcion');
            $table->enum('estado_suscripcion', ['pendiente', 'activa', 'cancelado'])->default('pendiente');
            $table->enum('sector', ['cafetería', 'restaurante', 'otro'])->nullable()->default('otro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
