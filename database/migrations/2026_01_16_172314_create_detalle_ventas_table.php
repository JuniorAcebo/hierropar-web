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
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();

            $table->integer('cantidad');

            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->decimal('precio_total', 20, 4)->default(0);

            $table->string('nota_personal',255)->nullable();
            $table->string('nota_cliente',255)->nullable();
            
            $table->primary(['producto_id', 'venta_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
