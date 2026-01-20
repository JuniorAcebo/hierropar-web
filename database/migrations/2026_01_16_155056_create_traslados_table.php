<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('traslados', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_hora');
            $table->string('origen',255);
            $table->string('destino',255);
            $table->decimal('costo_envio', 10, 2);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('traslados');
    }
};
