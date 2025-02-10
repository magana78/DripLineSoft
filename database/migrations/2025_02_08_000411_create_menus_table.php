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
        Schema::create('menus', function (Blueprint $table) {
            $table->integer('id_menu', true);
            $table->integer('id_sucursal')->index('id_sucursal');
            $table->string('nombre_menu');
            $table->enum('categoria', ['bebidas calientes', 'bebidas frías', 'postres', 'snacks', 'promociones']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
