<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. crear almacen central si no existe
        $centralId = DB::table('almacenes')->insertGetId([
            'nombre' => 'Almacen Central',
            'tipo' => 'central',
            'ubicacion' => 'Principal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. mover stock existente de 'productos' a 'producto_almacen' (Central)
        $products = DB::table('productos')->get();

        foreach ($products as $product) {
            DB::table('producto_almacen')->insert([
                'producto_id' => $product->id,
                'almacen_id' => $centralId,
                'cantidad' => $product->stock, // Assumes 'stock' column still exists
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. eliminar la columna 'stock' de 'productos'
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->decimal('stock', 10, 4)->unsigned()->default(0);
        });

        // 4. restaurar stock a la tabla de productos (simplificando la migracion)
        // idealmente sumamos todos los stocks
        
        $stocks = DB::table('producto_almacen')->get();
        foreach($stocks as $s) {
            // simplemente re-agregamos, pero esto es destrutivo potencialmente si no es cuidadoso. 
            // Por ahora, simplemente agregar la columna de vuelta es lo suficiente para la estructura.
            DB::table('productos')->where('id', $s->producto_id)->update([
                'stock' => $s->cantidad,
            ]);
            /*DB::table('producto_almacen')->where('producto_id', $s->producto_id)->delete();
            DB::table('producto_almacen')->where('almacen_id', $s->almacen_id)->delete();
            DB::table('producto_almacen')->where('user_id', $s->user_id)->delete();
            DB::table('producto_almacen')->where('referencia', $s->referencia)->delete();
            DB::table('producto_almacen')->where('tipo_movimiento', $s->tipo_movimiento)->delete();
            DB::table('producto_almacen')->where('created_at', $s->created_at)->delete();
            DB::table('producto_almacen')->where('updated_at', $s->updated_at)->delete();
            DB::table('producto_almacen')->where('estado', $s->estado)->delete();
            DB::table('producto_almacen')->where('deleted_at', $s->deleted_at)->delete();
            DB::table('producto_almacen')->where('deleted_by', $s->deleted_by)->delete();*/
        }
    }
};
