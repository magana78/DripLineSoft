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
        Schema::table('clientes_metodos_pago', function (Blueprint $table) {
            $table->foreign(['id_cliente'], 'clientes_metodos_pago_ibfk_1')->references(['id_cliente'])->on('clientes')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_metodo_pago'], 'clientes_metodos_pago_ibfk_2')->references(['id_metodo_pago'])->on('metodos_pago')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes_metodos_pago', function (Blueprint $table) {
            $table->dropForeign('clientes_metodos_pago_ibfk_1');
            $table->dropForeign('clientes_metodos_pago_ibfk_2');
        });
    }
};
