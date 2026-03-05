<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cotizaciones', 'estado')) {
            Schema::table('cotizaciones', function (Blueprint $table) {
                $table->dropColumn('estado');
            });
        }

        $hasVentaId = Schema::hasColumn('cotizaciones', 'venta_id');
        $hasCompraId = Schema::hasColumn('cotizaciones', 'compra_id');

        if (!$hasVentaId || !$hasCompraId) {
            Schema::table('cotizaciones', function (Blueprint $table) use ($hasVentaId, $hasCompraId) {
                if (!$hasVentaId) {
                    $table->foreignId('venta_id')->nullable()->after('user_id')->constrained('ventas')->nullOnDelete();
                }
                if (!$hasCompraId) {
                    $table->foreignId('compra_id')->nullable()->after($hasVentaId ? 'user_id' : 'venta_id')->constrained('compras')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cotizaciones', 'compra_id')) {
            Schema::table('cotizaciones', function (Blueprint $table) {
                $table->dropConstrainedForeignId('compra_id');
            });
        }
        if (Schema::hasColumn('cotizaciones', 'venta_id')) {
            Schema::table('cotizaciones', function (Blueprint $table) {
                $table->dropConstrainedForeignId('venta_id');
            });
        }

        if (!Schema::hasColumn('cotizaciones', 'estado')) {
            Schema::table('cotizaciones', function (Blueprint $table) {
                $table->enum('estado', ['pendiente', 'venta_realizada', 'compra_realizada', 'anulado'])->default('pendiente')->after('total');
            });
        }
    }
};
