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
        Schema::create('clientes_metodos_pago', function (Blueprint $table) {
            $table->integer('id_cliente_metodo_pago', true);
            $table->integer('id_cliente')->index('id_cliente');
            $table->integer('id_metodo_pago')->index('id_metodo_pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes_metodos_pago');
    }
};
