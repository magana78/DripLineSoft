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
        Schema::create('sucursales', function (Blueprint $table) {
            $table->integer('id_sucursal', true);
            $table->integer('id_cliente')->index('id_cliente');
            $table->string('nombre_sucursal');
            $table->text('direccion')->nullable();
            $table->decimal('latitud', 10, 7)->nullable();  // Nueva columna para latitud
            $table->decimal('longitud', 10, 7)->nullable(); // Nueva columna para longitud
            $table->string('telefono', 20)->nullable();
            $table->string('horario_atencion')->nullable();
            $table->integer('tiempo_entrega_estandar')->nullable()->default(30)->comment('Tiempo estándar de entrega en minutos');
            $table->boolean('activa')->default(true); // Nueva columna para la bandera de estado

        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sucursales');
    }
};
