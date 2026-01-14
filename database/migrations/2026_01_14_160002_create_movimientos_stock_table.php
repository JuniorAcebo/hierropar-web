<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('movimientos_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('almacen_origen_id')->nullable()->constrained('almacenes')->onDelete('set null');
            $table->foreignId('almacen_destino_id')->nullable()->constrained('almacenes')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Who moved it
            $table->decimal('cantidad', 10, 4);
            $table->string('tipo_movimiento'); // transferencia, venta, compra, ajuste_entrada, ajuste_salida
            $table->string('referencia')->nullable(); // e.g. Order #123
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('movimientos_stock');
    }
};
