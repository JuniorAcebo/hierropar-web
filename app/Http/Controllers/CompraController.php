<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Models\Compra;
use App\Models\Comprobante;
use App\Models\Producto;
use App\Models\Proveedore;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class CompraController extends Controller
{

    public function generarPdf($id, Request $request)
    {
        $compra = Compra::with([
            'comprobante',
            'proveedore.persona',
            'productos' => fn($q) => $q->withPivot('cantidad', 'precio_compra', 'precio_venta')
        ])->findOrFail($id);

        // Formateo de fechas y números (mantén tu código actual)

        $pdf = Pdf::loadView('compra.pdf', compact('compra'))
            ->setPaper('a4', 'portrait');

        $fileName = "COMPRA-{$compra->numero_comprobante}-{$compra->proveedore->persona->razon_social}.pdf";

        if ($request->has('print')) {
            return $pdf->stream($fileName); // Cambiamos download por stream
        }

        return $pdf->download($fileName);
    }

    function __construct()
    {
        $this->middleware('permission:ver-compra|crear-compra|mostrar-compra|eliminar-compra', ['only' => ['index']]);
        $this->middleware('permission:crear-compra', ['only' => ['create', 'store']]);
        $this->middleware('permission:mostrar-compra', ['only' => ['show']]);
        $this->middleware('permission:editar-compra', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-compra', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $compras = Compra::with('comprobante', 'proveedore.persona')
            ->where('estado', 1)
            ->latest()
            ->get();

        return view('compra.index', compact('compras'));
    }

    
    public function create()
    {
       
    }

    public function store(StoreCompraRequest $request)
    {
        try {
            DB::beginTransaction();

            // Crear la compra
            $compraData = $request->validated();
            $compraData['fecha_hora'] = now();

            $compra = Compra::create($compraData);

            // Procesar productos
            $this->processProductos($compra, $request);

            DB::commit();

            return redirect()->route('compras.index')
                ->with('success', 'Compra registrada exitosamente');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar la compra: ' . $e->getMessage());
        }
    }

    protected function processProductos(Compra $compra, Request $request)
    {
        $productos = $request->get('arrayidproducto', []);
        $cantidades = $request->get('arraycantidad', []);
        $preciosCompra = $request->get('arraypreciocompra', []);
        $preciosVenta = $request->get('arrayprecioventa', []);

        // Validación de consistencia
        if (
            count($productos) !== count($cantidades) ||
            count($productos) !== count($preciosCompra) ||
            count($productos) !== count($preciosVenta)
        ) {
            throw new Exception('Datos de productos inconsistentes');
        }

        // Agrupar productos por ID para sumar cantidades
        $productosAgrupados = [];
        foreach ($productos as $index => $productoId) {
            $cantidad = floatval($cantidades[$index]);
            $precioCompra = floatval($preciosCompra[$index]);
            $precioVenta = floatval($preciosVenta[$index]);

            if (isset($productosAgrupados[$productoId])) {
                $productosAgrupados[$productoId]['cantidad'] += $cantidad;
                // Mantener el último precio ingresado
                $productosAgrupados[$productoId]['precio_compra'] = $precioCompra;
                $productosAgrupados[$productoId]['precio_venta'] = $precioVenta;
            } else {
                $productosAgrupados[$productoId] = [
                    'cantidad' => $cantidad,
                    'precio_compra' => $precioCompra,
                    'precio_venta' => $precioVenta
                ];
            }
        }

        // Procesar productos agrupados
        foreach ($productosAgrupados as $productoId => $detalle) {
            $producto = Producto::findOrFail($productoId);

            if ($detalle['precio_venta'] < $detalle['precio_compra']) {
                throw new Exception("El precio de venta no puede ser menor al precio de compra para: {$producto->nombre}");
            }

            // Adjuntar producto a la compra
            $compra->productos()->attach($productoId, [
                'cantidad' => $detalle['cantidad'],
                'precio_compra' => $detalle['precio_compra'],
                'precio_venta' => $detalle['precio_venta']
            ]);

            // Actualizar stock usando increment (seguro para decimales)
            $producto->increment('stock', $detalle['cantidad']);

            // Actualizar precios actuales del producto
            $producto->update([
                'precio_compra' => $detalle['precio_compra'],
                'precio_venta' => $detalle['precio_venta']
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    // En CompraController.php
    public function show(Compra $compra)
    {
        // Verificar si es una petición AJAX
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('compra.show-modal', compact('compra'))->render()
            ]);
        }

        return view('compra.show', compact('compra'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $compra = Compra::with([
                'comprobante',
                'proveedore.persona',
                'productos' => fn($q) => $q->withPivot('cantidad', 'precio_compra', 'precio_venta')
            ])->findOrFail($id);

            // Prepara información sobre productos vendidos
            $productosInfo = [];
            foreach ($compra->productos as $producto) {
                $vendido = $producto->ventas()->sum('cantidad');
                $productosInfo[$producto->id] = [
                    'vendido' => $vendido,
                    'minimo_permitido' => $vendido, // El mínimo permitido es lo ya vendido
                    'cantidad_original' => $producto->pivot->cantidad
                ];
            }

            return view('compra.edit', [
                'compra' => $compra,
                'proveedores' => Proveedore::with('persona')
                    ->whereHas('persona', fn($q) => $q->where('estado', 1))
                    ->get(),
                'comprobantes' => Comprobante::all(),
                'productos' => Producto::where('estado', 1)->get(),
                'productosInfo' => $productosInfo
            ]);
        } catch (Exception $e) {
            return redirect()->route('compras.index')
                ->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $compra = Compra::with('productos')->findOrFail($id);
            $productosInfo = [];
            $nuevosProductos = [];

            // 1. Validar productos vendidos y calcular máximos permitidos
            foreach ($compra->productos as $producto) {
                $vendido = $producto->ventas()->sum('cantidad');
                $productosInfo[$producto->id] = [
                    'vendido' => $vendido,
                    'cantidad_original' => $producto->pivot->cantidad,
                    'minimo_permitido' => $vendido
                ];
            }

            // 2. Validar y preparar nuevos datos - Agrupar por producto ID
            $productosAgrupados = [];
            foreach ($request->arrayidproducto as $index => $productoId) {
                $producto = Producto::findOrFail($productoId);
                $nuevaCantidad = floatval($request->arraycantidad[$index]);
                $precioCompra = floatval($request->arraypreciocompra[$index]);
                $precioVenta = floatval($request->arrayprecioventa[$index]);

                // Validaciones
                if ($precioVenta < $precioCompra) {
                    throw new Exception("Precio de venta menor al de compra para {$producto->nombre}");
                }

                // Agrupar por producto ID - si ya existe, sumar cantidades
                if (isset($productosAgrupados[$productoId])) {
                    $productosAgrupados[$productoId]['cantidad'] += $nuevaCantidad;
                    // Mantener el último precio ingresado (o puedes promediarlos si prefieres)
                    $productosAgrupados[$productoId]['precio_compra'] = $precioCompra;
                    $productosAgrupados[$productoId]['precio_venta'] = $precioVenta;
                } else {
                    $productosAgrupados[$productoId] = [
                        'cantidad' => $nuevaCantidad,
                        'precio_compra' => $precioCompra,
                        'precio_venta' => $precioVenta
                    ];
                }

                // Validación de cantidad mínima para productos existentes
                if (isset($productosInfo[$productoId])) {
                    if ($productosAgrupados[$productoId]['cantidad'] < $productosInfo[$productoId]['minimo_permitido']) {
                        throw new Exception(
                            "No puede reducir la cantidad de {$producto->nombre} a {$productosAgrupados[$productoId]['cantidad']}. " .
                                "Mínimo permitido: {$productosInfo[$productoId]['minimo_permitido']} " .
                                "(ya vendido: {$productosInfo[$productoId]['vendido']})"
                        );
                    }
                }
            }

            // 3. Revertir solo el stock NO vendido de los productos originales
            foreach ($compra->productos as $producto) {
                $vendido = $productosInfo[$producto->id]['vendido'];
                $diferencia = $producto->pivot->cantidad - $vendido;

                if ($diferencia > 0) {
                    $producto->decrement('stock', $diferencia);
                }
            }

            // 4. Actualizar la compra con los productos agrupados
            $compra->productos()->detach();
            $compra->productos()->attach($productosAgrupados);

            // Calcular nuevo total
            $total = 0;
            foreach ($productosAgrupados as $productoId => $detalle) {
                $total += $detalle['cantidad'] * $detalle['precio_compra'];
            }

            $compra->update([
                'numero_comprobante' => $request->numero_comprobante,
                'fecha_hora' => $request->fecha_hora ?? now(),
                'total' => $total,
                'comprobante_id' => $request->comprobante_id,
                'proveedore_id' => $request->proveedore_id
            ]);

            // 5. Aplicar nuevo stock (solo lo nuevo no vendido)
            foreach ($productosAgrupados as $productoId => $detalle) {
                $producto = Producto::find($productoId);
                $vendido = $productosInfo[$productoId]['vendido'] ?? 0;
                $diferencia = $detalle['cantidad'] - $vendido;

                if ($diferencia > 0) {
                    $producto->increment('stock', $diferencia);
                }

                // Actualizar precios del producto (solo si cambió)
                $producto->update([
                    'precio_compra' => $detalle['precio_compra'],
                    'precio_venta' => $detalle['precio_venta']
                ]);
            }

            DB::commit();
            return redirect()->route('compras.index')
                ->with('success', 'Compra actualizada exitosamente');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
    public function destroy(string $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $compra = Compra::with(['productos' => function ($query) {
                    $query->lockForUpdate(); // Bloquea los productos para evitar race conditions
                }])->findOrFail($id);

                // Verificar primero TODOS los productos antes de hacer cualquier cambio
                foreach ($compra->productos as $producto) {
                    $vendidoTotal = $producto->ventas()->sum('cantidad');
                    $cantidadComprada = floatval($producto->pivot->cantidad);
                    $stockActual = floatval($producto->stock);

                    // Si se vendió más de lo que se quiere revertir
                    if ($vendidoTotal > 0) {
                        $stockDespuesDeRevertir = $stockActual - $cantidadComprada;

                        if ($stockDespuesDeRevertir < $vendidoTotal) {
                            throw new Exception(
                                "No se puede eliminar: El producto '{$producto->nombre}' " .
                                    "tiene {$vendidoTotal} unidades vendidas. " .
                                    "Si se revierten {$cantidadComprada} unidades, " .
                                    "el stock quedaría en {$stockDespuesDeRevertir} " .
                                    "(insuficiente para las ventas existentes)."
                            );
                        }
                    }

                    // Verificar que no quede stock negativo
                    if ($stockActual < $cantidadComprada) {
                        throw new Exception(
                            "Stock insuficiente para revertir '{$producto->nombre}'. " .
                                "Stock actual: {$stockActual}, se necesita revertir: {$cantidadComprada}"
                        );
                    }
                }

                // Si todas las validaciones pasan, proceder con la reversión
                foreach ($compra->productos as $producto) {
                    $producto->decrement('stock', floatval($producto->pivot->cantidad));
                }

                $compra->productos()->detach();
                $compra->delete();
            });

            return redirect()->route('compras.index')
                ->with('success', 'Compra eliminada y stock revertido exitosamente');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()->route('compras.index')
                ->with('error', 'Error de base de datos: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->route('compras.index')
                ->with('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
    }
}
