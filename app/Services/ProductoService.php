<?php

namespace App\Services;

use App\Models\Producto;
use App\Models\InventarioAlmacen;
use Illuminate\Support\Facades\DB;

class ProductoService
{
    /**
     * Ajustar stock de un producto en un almacén específico
     * @param string $tipo 'sumar', 'restar', 'fijar'
     */
    public function ajustarStock(int $productoId, int $almacenId, float $cantidad, int $userId, string $tipo = 'fijar', string $motivo = 'Ajuste manual')
    {
        return DB::transaction(function () use ($productoId, $almacenId, $cantidad, $userId, $tipo, $motivo) {
            $inventario = InventarioAlmacen::where('producto_id', $productoId)
                ->where('almacen_id', $almacenId)
                ->first();

            $cantidadAnterior = $inventario ? $inventario->stock : 0;

            $nuevaCantidad = $cantidad;
            if ($tipo === 'sumar') {
                $nuevaCantidad = $cantidadAnterior + $cantidad;
            } elseif ($tipo === 'restar') {
                $nuevaCantidad = $cantidadAnterior - $cantidad;
            }

            // Actualizar o crear inventario
            $inventario = InventarioAlmacen::updateOrCreate(
                ['producto_id' => $productoId, 'almacen_id' => $almacenId],
                ['stock' => $nuevaCantidad]
            );

            // Registrar en el historial de ajustes
            \App\Models\AjusteStock::create([
                'producto_id' => $productoId,
                'almacen_id' => $almacenId,
                'user_id' => $userId,
                'cantidad_anterior' => $cantidadAnterior,
                'cantidad_nueva' => $nuevaCantidad,
                'motivo' => $motivo
            ]);

            return $inventario;
        });
    }

    /**
     * Eliminar producto completamente
     */
    public function eliminarProducto(Producto $producto)
    {
        return DB::transaction(function () use ($producto) {
            // Eliminar inventarios relacionados
            $producto->inventarios()->delete();

            // Eliminar producto
            return $producto->delete();
        });
    }
    // Otros métodos relacionados con productos pueden ir aquí
    /**
     * metodo para evitar borrar productos que estan en uso de una orden de compra o venta
     * metodo para
     */
}
