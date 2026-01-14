<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('descuentos_grupo_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos_clientes')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->decimal('descuento_porcentaje', 5, 2)->nullable(); // Specific discount %
            $table->timestamps();
            
            $table->unique(['grupo_id', 'producto_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('descuentos_grupo_producto');
    }
};
