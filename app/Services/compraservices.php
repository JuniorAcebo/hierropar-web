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
     * Utiliza lockForUpdate para evitar errores de concurrencia.
     */
    public function procesarCompra(Compra $compra): array
    {
        return DB::transaction(function () use ($compra) {
            try {
                $almacenId = $compra->almacen_id;

                foreach ($compra->detalles as $detalle) {
                    // Buscamos o creamos, pero bloqueamos la fila para escritura
                    // Nota: firstOrCreate no soporta lockForUpdate directamente en una línea,
                    // así que lo hacemos en dos pasos seguros.

                    $inventario = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                        ->where('almacen_id', $almacenId)
                        ->lockForUpdate() // Bloquea la fila hasta que termine la transacción
                        ->first();

                    if (!$inventario) {
                        $inventario = new InventarioAlmacen();
                        $inventario->producto_id = $detalle->producto_id;
                        $inventario->almacen_id = $almacenId;
                        $inventario->stock = 0;
                    }

                    $inventario->stock += $detalle->cantidad;
                    $inventario->save();

                    // Opcional: Aquí llamarías a una función para guardar en historial_ajustes
                    // $this->registrarKardex($detalle, 'COMPRA');
                }

                return $this->successResponse('Stock actualizado correctamente.');

            } catch (Exception $e) {
                Log::error("Error procesando compra ID {$compra->id}: " . $e->getMessage());
                // Hacemos rollback manual o dejamos que DB::transaction lo haga lanzando la excepción
                throw $e;
            }
        });
    }

    /**
     * Revierte el stock (Anular/Eliminar Compra).
     * Primero valida si es posible realizar la reversión.
     */
    public function revertirCompra(Compra $compra): array
    {
        return DB::transaction(function () use ($compra) {
            try {
                // 1. Validar primero si se puede eliminar
                $validacion = $this->validarReversion($compra);
                if (!$validacion['success']) {
                    return $validacion; // Retornamos el error específico
                }

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

                return $this->successResponse('Compra revertida y stock ajustado correctamente.');

            } catch (Exception $e) {
                Log::error("Error revirtiendo compra ID {$compra->id}: " . $e->getMessage());
                return $this->errorResponse('Error interno al revertir la compra.');
            }
        });
    }

    /**
     * Método de Validación (antiguo candeletecompra).
     * Verifica si hay stock suficiente para eliminar la compra sin romper la integridad.
     */
    public function validarReversion(Compra $compra): array
    {
        $almacenId = $compra->almacen_id;
        $errores = [];

        foreach ($compra->detalles as $detalle) {
            $inventario = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                ->where('almacen_id', $almacenId)
                ->first(); // Aquí no necesitamos lock, solo lectura

            $stockActual = $inventario ? $inventario->stock : 0;
            $stockFuturo = $stockActual - $detalle->cantidad;

            if ($stockFuturo < 0) {
                // Mensaje detallado: Qué producto falla y cuánto falta
                $nombreProducto = $detalle->producto->nombre ?? 'Producto #' . $detalle->producto_id;
                $errores[] = "El producto '{$nombreProducto}' quedaría con stock negativo ({$stockFuturo}). Stock actual: {$stockActual}, Intenta retirar: {$detalle->cantidad}.";
            }
        }

        if (count($errores) > 0) {
            return $this->errorResponse('No se puede eliminar la compra porque dejaría stock negativo.', $errores);
        }

        return $this->successResponse('Validación exitosa.');
    }

    /**
     * Método Nuevo: Gestionar Edición de Compra.
     * Compara los detalles viejos con los nuevos para calcular la diferencia neta de stock.
     * * @param Compra $compra
     * @param array $nuevosDetalles Array con estructura ['producto_id' => int, 'cantidad' => float]
     */
    public function actualizarStockPorEdicion(Compra $compra, array $nuevosDetalles): array
    {
        return DB::transaction(function () use ($compra, $nuevosDetalles) {
            try {
                $almacenId = $compra->almacen_id;

                // Mapear detalles actuales para acceso rápido: [producto_id => cantidad]
                $detallesActuales = $compra->detalles->pluck('cantidad', 'producto_id')->toArray();

                // Mapear nuevos detalles
                $detallesNuevos = collect($nuevosDetalles)->pluck('cantidad', 'producto_id')->toArray();

                // Unimos todos los IDs de productos involucrados
                $todosLosProductos = array_unique(array_merge(array_keys($detallesActuales), array_keys($detallesNuevos)));

                foreach ($todosLosProductos as $productoId) {
                    $cantVieja = $detallesActuales[$productoId] ?? 0;
                    $cantNueva = $detallesNuevos[$productoId] ?? 0;

                    // Diferencia: Si era 10 y ahora es 8, diff es -2 (hay que restar stock)
                    // Si era 10 y ahora es 15, diff es +5 (hay que sumar stock)
                    $diferencia = $cantNueva - $cantVieja;

                    if ($diferencia == 0) continue;

                    $inventario = InventarioAlmacen::where('producto_id', $productoId)
                        ->where('almacen_id', $almacenId)
                        ->lockForUpdate()
                        ->first();

                    if (!$inventario && $diferencia > 0) {
                        // Crear si no existe y estamos sumando
                        $inventario = new InventarioAlmacen();
                        $inventario->producto_id = $productoId;
                        $inventario->almacen_id = $almacenId;
                        $inventario->stock = 0;
                    } elseif (!$inventario && $diferencia < 0) {
                         // Error critico: intentar restar a algo que no existe
                         throw new Exception("Inconsistencia de inventario para producto ID {$productoId}");
                    }

                    // Validación de stock negativo antes de guardar
                    if (($inventario->stock + $diferencia) < 0) {
                        return $this->errorResponse("No se puede editar: El producto ID {$productoId} quedaría en negativo.");
                    }

                    $inventario->stock += $diferencia;
                    $inventario->save();
                }

                return $this->successResponse('Compra editada y stock ajustado.');

            } catch (Exception $e) {
                Log::error("Error editando compra ID {$compra->id}: " . $e->getMessage());
                return $this->errorResponse('Error al procesar la edición de la compra.');
            }
        });
    }

    // --- Helpers para respuestas estandarizadas ---

    private function successResponse(string $message, $data = []): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data'    => $data,
            'code'    => 200
        ];
    }

    private function errorResponse(string $message, $errors = [], int $code = 400): array
    {
        return [
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
            'code'    => $code
        ];
    }
}
