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
        Schema::table('detalles_pedido', function (Blueprint $table) {
            $table->foreign(['id_pedido'], 'detalles_pedido_ibfk_1')->references(['id_pedido'])->on('pedidos')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['id_producto'], 'detalles_pedido_ibfk_2')->references(['id_producto'])->on('productos')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalles_pedido', function (Blueprint $table) {
            $table->dropForeign('detalles_pedido_ibfk_1');
            $table->dropForeign('detalles_pedido_ibfk_2');
        });
    }
};
