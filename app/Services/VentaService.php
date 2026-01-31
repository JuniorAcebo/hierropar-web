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

            // Asegurar que detalles y productos estén cargados
            $venta->loadMissing('detalles.producto.tipounidad');

            foreach ($venta->detalles as $detalle) {
                if ($detalle->producto->tipounidad && !$detalle->producto->tipounidad->maneja_stock) {
                    continue; // No descontar stock si es servicio
                }

                $inventario = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                    ->where('almacen_id', $almacenId)
                    ->lockForUpdate()
                    ->first();

                if (!$inventario || $inventario->stock < $detalle->cantidad) {
                    throw new Exception("Stock insuficiente para el producto: {$detalle->producto->nombre}. Disponible: " . ($inventario->stock ?? 0));
                }

                $inventario->stock -= $detalle->cantidad;
                $inventario->save();
            }
        });
    }

    /**
     * Revierte el stock (al eliminar o editar venta).
     */
    public function revertirStock(Venta $venta)
    {
        return DB::transaction(function () use ($venta) {
            $almacenId = $venta->almacen_id;
            
            // Usar el almacén original de la venta
            $venta->loadMissing('detalles.producto.tipounidad');

            foreach ($venta->detalles as $detalle) {
                if ($detalle->producto->tipounidad && !$detalle->producto->tipounidad->maneja_stock) {
                    continue; 
                }

                $inventario = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                    ->where('almacen_id', $almacenId)
                    ->lockForUpdate()
                    ->first();

                if (!$inventario) {
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
     * Valida stock antes de crear venta de forma masiva para evitar N+1.
     */
    public function validarStockDisponible($items, $almacenId)
    {
        $productoIds = array_column($items, 'producto_id');
        $productos = \App\Models\Producto::with('tipounidad')->whereIn('id', $productoIds)->get()->keyBy('id');
        
        foreach ($items as $item) {
            $producto = $productos->get($item['producto_id']);
            
            if (!$producto) continue;
            if ($producto->tipounidad && !$producto->tipounidad->maneja_stock) continue;

            $inventario = InventarioAlmacen::where('producto_id', $item['producto_id'])
                ->where('almacen_id', $almacenId)
                ->first();
            
            $stock = $inventario ? $inventario->stock : 0;
            
            if ($stock < $item['cantidad']) {
                throw new Exception("Stock insuficiente para {$producto->nombre}. Stock actual: {$stock}, Solicitado: {$item['cantidad']}");
            }
        }
    }
}
