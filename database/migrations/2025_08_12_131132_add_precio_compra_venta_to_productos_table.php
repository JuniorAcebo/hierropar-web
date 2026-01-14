<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('precio_compra', 10, 2)->default(0)->after('img_path');
            $table->decimal('precio_venta', 10, 2)->default(0)->after('precio_compra');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['precio_compra', 'precio_venta']);
        });
    }
};
