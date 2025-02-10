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
        Schema::create('inventario', function (Blueprint $table) {
            $table->integer('id_inventario', true);
            $table->integer('id_sucursal')->index('id_sucursal');
            $table->string('nombre_item');
            $table->integer('cantidad');
            $table->string('unidad_medida', 50);
            $table->integer('umbral_minimo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};
