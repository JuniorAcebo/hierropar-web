<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // nota personal es para el usuario
        // nota cliente es para el cliente
        //alamacen donde se hizo la venta (solo resta stock) de ese almacen osea si se vende en la central se resta stock a la central
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_hora');
            $table->string('numero_comprobante',255);
            $table->decimal('total',8,2,true);
            // pagado, pendiente, por entregar
            $table->enum('estado', ['pagado', 'pendiente', 'por entregar'])->default('pendiente');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('almacen_id')->nullable()->constrained('almacenes')->onDelete('set null');
            $table->foreignId('comprobante_id')->nullable()->constrained('comprobantes')->onDelete('set null');
            $table->string('nota_personal')->nullable();
            $table->string('nota_cliente')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ventas');
    }
};
