<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('producto_almacen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('almacen_id')->constrained('almacenes')->onDelete('cascade');
            $table->decimal('cantidad', 10, 4)->default(0);
            $table->timestamps();

            $table->unique(['producto_id', 'almacen_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('producto_almacen');
    }
};
