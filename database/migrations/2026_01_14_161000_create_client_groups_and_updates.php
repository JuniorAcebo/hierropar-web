<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('grupos_clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('porcentaje_descuento_general', 5, 2)->default(0); // 0.00 to 100.00
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->foreignId('grupo_id')->nullable()->constrained('grupos_clientes')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['grupo_id']);
            $table->dropColumn('grupo_id');
        });
        Schema::dropIfExists('grupos_clientes');
    }
};
