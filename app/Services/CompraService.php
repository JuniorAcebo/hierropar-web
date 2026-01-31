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
            
            // Cargar relaciones necesarias
            $compra->loadMissing('detalles.producto.tipounidad');

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

                // Actualizar precios siempre
                if ($detalle->producto) {
                    $detalle->producto->update([
                        'precio_compra' => $detalle->precio_compra,
                        'precio_venta' => $detalle->precio_venta
                    ]);
                }

                if ($detalle->producto->tipounidad && !$detalle->producto->tipounidad->maneja_stock) {
                    continue; 
                }

                $inventario->stock += $detalle->cantidad;
                $inventario->save();
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
            
            if (!$almacenId) return;

            $compra->loadMissing('detalles.producto.tipounidad');

            foreach ($compra->detalles as $detalle) {
                if (!$detalle->producto) continue;

                $inventario = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                    ->where('almacen_id', $almacenId)
                    ->lockForUpdate()
                    ->first();

                if ($detalle->producto->tipounidad && !$detalle->producto->tipounidad->maneja_stock) {
                    continue; 
                }

                if ($inventario) {
                    $inventario->stock -= $detalle->cantidad;
                    $inventario->save();
                }
            }
        });
    }

    /**
     * Valida si es posible revertir o editar una compra sin dejar stock negativo.
     * Importante: Llamar ANTES de revertirStock.
     */
    public function validarReversion(Compra $compra)
    {
        // Asegurar que las relaciones están cargadas de forma fresca para validación
        $compra->load('detalles.producto.tipounidad');
        $almacenId = $compra->almacen_id;

        if (!$almacenId) {
            Log::warning("Compra ID {$compra->id} no tiene almacen_id asignado.");
            return; // Si no hay almacén, no podemos validar stock (o asumimos que no hay)
        }

        foreach ($compra->detalles as $detalle) {
            $producto = $detalle->producto;
            
            // Si el producto no existe o no maneja stock, no validamos
            if (!$producto) continue;
            
            // Cargar tipounidad si no existe para evitar errores
            if (!$producto->relationLoaded('tipounidad')) {
                $producto->load('tipounidad');
            }

            if ($producto->tipounidad && !$producto->tipounidad->maneja_stock) {
                continue; 
            }

            // Consultar stock actual de forma fresca
            $inventario = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                ->where('almacen_id', $almacenId)
                ->first();

            $stockActual = $inventario ? (float)$inventario->stock : 0.0;
            $cantidadReversion = (float)$detalle->cantidad;

            if ($stockActual < $cantidadReversion) {
                $msg = "Error de Inventario: No se puede modificar la compra del producto '{$producto->nombre}' porque el stock actual en el almacén ({$stockActual}) es menor a la cantidad registrada en esta compra ({$cantidadReversion}). Probablemente ya se vendió parte de esta mercadería.";
                Log::error($msg);
                throw new Exception($msg);
            }
        }
    }
}
