<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cortes_tablero', function (Blueprint $table) {
            $table->id();

            // Relación con el cliente
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');

            // Información general del trabajo
            $table->string('nombre_trabajo');
            $table->text('descripcion')->nullable();

            // Medidas del tablero completo (en centímetros)
            $table->decimal('largo_tablero', 10, 2);  // en cm
            $table->decimal('ancho_tablero', 10, 2);  // en cm
            $table->integer('cantidad_tableros')->default(1);

            // Cortes/piezas (almacenaremos como JSON)
            $table->json('piezas')->nullable();

            // Totales
            $table->integer('total_piezas')->default(0);
            $table->integer('total_cortes')->default(0);

            // Estado
            $table->enum('estado', ['pendiente', 'en_proceso', 'completado'])->default('pendiente');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cortes_tablero');
    }
};
