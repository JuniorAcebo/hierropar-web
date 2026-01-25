<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\InventarioAlmacen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CompraService
{
    /**
     * Procesa la entrada de stock al confirmar una compra.
     */
    public function procesarEntradaStock(Compra $compra)
    {
        return DB::transaction(function () use ($compra) {
            $almacenId = $compra->almacen_id;

            foreach ($compra->detalles as $detalle) {
                $inventario = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                    ->where('almacen_id', $almacenId)
                    ->lockForUpdate()
                    ->first();

                if (!$inventario) {
                    $inventario = InventarioAlmacen::create([
                        'producto_id' => $detalle->producto_id,
                        'almacen_id' => $almacenId,
                        'stock' => 0
                    ]);
                }

                $inventario->stock += $detalle->cantidad;
                $inventario->save();

                // Actualizar precios del producto
                $detalle->producto->update([
                    'precio_compra' => $detalle->precio_compra,
                    'precio_venta' => $detalle->precio_venta
                ]);
            }
        });
    }

    /**
     * Revierte el stock (al eliminar o editar compra).
     */
    public function revertirStock(Compra $compra)
    {
        return DB::transaction(function () use ($compra) {
            $almacenId = $compra->almacen_id;

            foreach ($compra->detalles as $detalle) {
                $inventario = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                    ->where('almacen_id', $almacenId)
                    ->lockForUpdate()
                    ->first();

                if ($inventario) {
                    $inventario->stock -= $detalle->cantidad;
                    $inventario->save();
                }
            }
        });
    }

    /**
     * Valida si es posible revertir una compra sin dejar stock negativo.
     */
    public function validarReversion(Compra $compra)
    {
        $almacenId = $compra->almacen_id;

        foreach ($compra->detalles as $detalle) {
            $inventario = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                ->where('almacen_id', $almacenId)
                ->first();

            $stockActual = $inventario ? $inventario->stock : 0;
            if (($stockActual - $detalle->cantidad) < 0) {
                throw new Exception("Stock insuficiente para el producto: {$detalle->producto->nombre}. Stock actual: {$stockActual}, se intenta retirar: {$detalle->cantidad}");
            }
        }
    }
}
