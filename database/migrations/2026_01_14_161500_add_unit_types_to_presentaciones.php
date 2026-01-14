<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Adding columns to caracteristicas (assuming this links to Presentation/Unit logic broadly)
        // Or better, add to 'presentaciones' or 'productos' depending on where Unit is defined.
        // User said: "tipo de unidad-> parte de medida del 'tipo de candidad'"
        // Earlier I saw 'presentaciones' has 'caracteristica_id'.
        // Let's add it to 'caracteristicas' or 'productos'.
        // If I look at 'productos', it has 'presentacione_id'.
        // Let's assume 'presentaciones' is the right place for Unit info?
        // Actually, usually Unit is a property of the Product OR the Presentation.
        // Let's add it to 'productos' or 'presentaciones'. 
        // Based on user: "tipo de unidad ... ejemplo si se vende: por servicio gratis"
        // This sounds like a property of the item being sold.
        
        Schema::table('presentaciones', function (Blueprint $table) {
            $table->string('tipo_unidad', 50)->default('unidad')->nullable(); 
            // Enum values could be: 'servicio_gratis', 'servicio_cobro', 'metro', 'kilo', 'unidad'
            $table->boolean('tiene_limite')->default(true); 
            // 'por servicio gratis->cantidad ilimitada', others have limits
        });
    }

    public function down()
    {
        Schema::table('presentaciones', function (Blueprint $table) {
            $table->dropColumn(['tipo_unidad', 'tiene_limite']);
        });
    }
};
