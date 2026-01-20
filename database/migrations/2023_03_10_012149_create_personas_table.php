<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social',80);
            $table->string('direccion',80);
            $table->string('tipo_persona',20);
            $table->boolean('estado')->default(true);
            $table->string('numero_documento', 20);

            $table->foreignId('documento_id')->unique()->constrained('documentos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('personas');
    }
};
