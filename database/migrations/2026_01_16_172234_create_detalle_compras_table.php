<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('detalle_compras', function (Blueprint $table) {

            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('compra_id')->constrained('compras')->cascadeOnDelete();

            $table->integer('cantidad');

            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->decimal('precio_total', 20, 4)->default(0);

            $table->string('nota_descriptiva',255)->nullable();
            
            $table->primary(['producto_id', 'compra_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_compras');
    }
};
