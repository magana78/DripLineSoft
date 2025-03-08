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
        Schema::create('cliente_usuario', function (Blueprint $table) {
            $table->id();
            $table->integer('id_cliente')->index();
            $table->integer('id_usuario')->index();
            $table->timestamps();

            // Llaves foráneas
            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');

            // Índice único para evitar duplicados
            $table->unique(['id_cliente', 'id_usuario']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_usuario');
    }
};
