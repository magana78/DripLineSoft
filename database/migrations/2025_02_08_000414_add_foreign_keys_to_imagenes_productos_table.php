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
        Schema::table('imagenes_productos', function (Blueprint $table) {
            $table->foreign(['id_producto'], 'imagenes_productos_ibfk_1')->references(['id_producto'])->on('productos')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('imagenes_productos', function (Blueprint $table) {
            $table->dropForeign('imagenes_productos_ibfk_1');
        });
    }
};
