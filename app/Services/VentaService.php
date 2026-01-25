<?php

namespace App\Services;

use App\Models\Venta;
use App\Models\InventarioAlmacen;
use Illuminate\Support\Facades\DB;
use Exception;

class VentaService
{
    /**
     * Procesa la salida de stock al confirmar una venta.
     */
    public function procesarSalidaStock(Venta $venta)
    {
        return DB::transaction(function () use ($venta) {
            $almacenId = $venta->almacen_id;

            foreach ($venta->detalles as $detalle) {
                $inventario = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                    ->where('almacen_id', $almacenId)
                    ->lockForUpdate()
                    ->first();

                if (!$inventario || $inventario->stock < $detalle->cantidad) {
                    throw new Exception("Stock insuficiente para el producto: {$detalle->producto->nombre}");
                }

                $inventario->stock -= $detalle->cantidad;
                $inventario->save();
            }
        });
    }

    /**
     * Revierte el stock (al eliminar o anular venta).
     */
    public function revertirStock(Venta $venta)
    {
        return DB::transaction(function () use ($venta) {
            $almacenId = $venta->almacen_id;

            foreach ($venta->detalles as $detalle) {
                $inventario = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                    ->where('almacen_id', $almacenId)
                    ->lockForUpdate()
                    ->first();

                if (!$inventario) {
                    // Si no existe inventario (raro si salió de ahí), se crea? 
                    // Mejor asumimos que existe o se crea uno nuevo con ese stock devuelto.
                    InventarioAlmacen::create([
                        'producto_id' => $detalle->producto_id,
                        'almacen_id' => $almacenId,
                        'stock' => $detalle->cantidad
                    ]);
                } else {
                    $inventario->stock += $detalle->cantidad;
                    $inventario->save();
                }
            }
        });
    }

    /**
     * Valida stock antes de crear venta.
     */
    public function validarStockDisponible($items, $almacenId)
    {
        foreach ($items as $item) {
            $inventario = InventarioAlmacen::where('producto_id', $item['producto_id'])
                ->where('almacen_id', $almacenId)
                ->first();
            
            $stock = $inventario ? $inventario->stock : 0;
            
            if ($stock < $item['cantidad']) {
                throw new Exception("Stock insuficiente para producto ID {$item['producto_id']}. Stock actual: {$stock}");
            }
        }
    }
}
