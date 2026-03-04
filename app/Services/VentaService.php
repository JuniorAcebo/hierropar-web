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

            // Cargar explícitamente para asegurar datos frescos
            $venta->load('detalles.producto.tipounidad');

            foreach ($venta->detalles as $detalle) {
                // Evitar crash si el producto fue eliminado
                if (!$detalle->producto) {
                    continue;
                }

                if ($detalle->producto->tipounidad && !$detalle->producto->tipounidad->maneja_stock) {
                    continue; // No descontar stock si es servicio
                }

                $inventario = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                    ->where('almacen_id', $almacenId)
                    ->lockForUpdate()
                    ->first();

                if (!$inventario || $inventario->stock < $detalle->cantidad) {
                    $nombre = $detalle->producto ? $detalle->producto->nombre : 'Producto eliminado';
                    throw new Exception("Stock insuficiente para el producto: {$nombre}. Disponible: " . ($inventario->stock ?? 0));
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

    /**
     * Centraliza la creación de una venta y sus detalles.
     */
    public function crearVenta(array $data, $userId)
    {
        return DB::transaction(function () use ($data, $userId) {
            // Mapear items para validación de stock
            $itemsParaStock = [];
            foreach ($data['arrayidproducto'] as $index => $productoId) {
                if (empty($productoId)) continue;
                $itemsParaStock[] = [
                    'producto_id' => $productoId,
                    'cantidad' => $data['arraycantidad'][$index]
                ];
            }
            
            $this->validarStockDisponible($itemsParaStock, $data['almacen_id']);

            $venta = Venta::create([
                'fecha_hora' => $data['fecha_hora'] ?? now(),
                'numero_comprobante' => $data['numero_comprobante'],
                'total' => 0,
                'metodo_pago' => $data['metodo_pago'] ?? 'efectivo',
                'monto_pagado' => 0,
                'estado_pago' => 'pendiente',
                'estado_entrega' => 'por_entregar',
                'cliente_id' => $data['cliente_id'],
                'almacen_id' => $data['almacen_id'],
                'comprobante_id' => $data['comprobante_id'],
                'user_id' => $userId,
                'nota_personal' => $data['nota_personal'] ?? null,
                'nota_cliente' => $data['nota_cliente'] ?? null,
            ]);

            $total = 0;
            foreach ($data['arrayidproducto'] as $index => $productoId) {
                $cantidad = floatval($data['arraycantidad'][$index] ?? 0);
                $precioVenta = floatval($data['arrayprecioventa'][$index] ?? 0);
                $descuento = floatval($data['arraydescuento'][$index] ?? 0);

                if (empty($productoId) || $cantidad <= 0 || $precioVenta <= 0) {
                    continue;
                }

                $venta->detalles()->create([
                    'producto_id' => $productoId,
                    'cantidad' => $cantidad,
                    'precio_venta' => $precioVenta,
                    'descuento' => $descuento
                ]);

                $total += ($cantidad * $precioVenta) - $descuento;
            }

            $montoPagado = (float) ($data['monto_pagado'] ?? 0);
            if (($data['estado_pago'] ?? '') === 'pagado') {
                $montoPagado = $total;
            }
            $montoPagado = max(0.0, min($montoPagado, (float) $total));
            $estadoPago = $montoPagado >= (float) $total ? 'pagado' : ($montoPagado > 0 ? 'parcial' : 'pendiente');

            $venta->update([
                'total' => $total,
                'monto_pagado' => $montoPagado,
                'estado_pago' => $estadoPago,
            ]);

            $this->procesarSalidaStock($venta);

            return $venta;
        });
    }

    /**
     * Centraliza la actualización de una venta.
     */
    public function actualizarVenta(Venta $venta, array $data)
    {
        return DB::transaction(function () use ($venta, $data) {
            // 1. Revertir stock (del almacen original)
            $this->revertirStock($venta);

            // 2. Eliminar detalles viejos
            $venta->detalles()->delete();

            // 3. Update Venta info básica
            $venta->update([
                'numero_comprobante' => $data['numero_comprobante'] ?? $venta->numero_comprobante,
                'fecha_hora' => $data['fecha_hora'] ?? $venta->fecha_hora,
                'cliente_id' => $data['cliente_id'],
                'almacen_id' => $data['almacen_id'],
                'comprobante_id' => $data['comprobante_id'],
                'nota_personal' => $data['nota_personal'] ?? null,
                'nota_cliente' => $data['nota_cliente'] ?? null,
            ]);

            // 4. Validar nuevo stock disponible
            $itemsParaStock = [];
            foreach ($data['arrayidproducto'] as $index => $productoId) {
                if (empty($productoId)) continue;
                $itemsParaStock[] = [
                    'producto_id' => $productoId,
                    'cantidad' => $data['arraycantidad'][$index]
                ];
            }
            $this->validarStockDisponible($itemsParaStock, $data['almacen_id']);

            // 5. Crear nuevos detalles y recalcular total
            $total = 0;
            foreach ($data['arrayidproducto'] as $index => $productoId) {
                $cantidad = floatval($data['arraycantidad'][$index] ?? 0);
                $precioVenta = floatval($data['arrayprecioventa'][$index] ?? 0);
                $descuento = floatval($data['arraydescuento'][$index] ?? 0);

                if (empty($productoId) || $cantidad <= 0 || $precioVenta <= 0) {
                    continue;
                }

                $venta->detalles()->create([
                    'producto_id' => $productoId,
                    'cantidad' => $cantidad,
                    'precio_venta' => $precioVenta,
                    'descuento' => $descuento
                ]);

                $total += ($cantidad * $precioVenta) - $descuento;
            }

            // 6. Ajustar pagos y estados
            $montoPagado = (float) ($data['monto_pagado'] ?? $venta->monto_pagado);
            if (($data['estado_pago'] ?? '') === 'pagado') {
                $montoPagado = $total;
            } elseif (($data['estado_pago'] ?? '') === 'pendiente') {
                $montoPagado = 0;
            }
            $montoPagado = max(0.0, min($montoPagado, (float) $total));
            $estadoPago = $montoPagado >= (float) $total ? 'pagado' : ($montoPagado > 0 ? 'parcial' : 'pendiente');

            $venta->update([
                'total' => $total,
                'metodo_pago' => $data['metodo_pago'] ?? $venta->metodo_pago,
                'monto_pagado' => $montoPagado,
                'estado_pago' => $estadoPago,
            ]);

            // 7. Salida stock nuevamente
            $venta->refresh();
            $this->procesarSalidaStock($venta);

            return $venta;
        });
    }
}
