<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTrasladoRequest;
use App\Http\Requests\UpdateTrasladoRequest;
use App\Models\Almacen;
use App\Models\InventarioAlmacen;
use App\Models\Producto;
use App\Models\Traslado;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrasladoController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-traslado', ['only' => ['index']]);
        $this->middleware('permission:crear-traslado', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-traslado', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-traslado', ['only' => ['destroy']]);
        $this->middleware('permission:update-estado', ['only' => ['updateEstado']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $traslados = Traslado::with(['user', 'origenAlmacen', 'destinoAlmacen', 'detalles.producto'])
            ->where('estado', 1)
            ->latest()
            ->get();

        return view('traslado.index', compact('traslados'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $almacenes = Almacen::where('estado', 1)
            ->get(['id', 'nombre']);

        $productos = Producto::where('estado', 1)
            ->with(['inventarios' => function ($q) {
                $q->where('stock', '>', 0);
            }])
            ->get(['id', 'codigo', 'nombre']);

        return view('traslado.create', compact('productos', 'almacenes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTrasladoRequest $request)
    {
        try {
            DB::beginTransaction();

            // Crear el traslado
            $trasladoData = $request->validated();
            $trasladoData['fecha_hora'] = now();
            $trasladoData['estado'] = 1;

            $traslado = Traslado::create($trasladoData);

            // Procesar productos
            $this->processProductos($traslado, $request);

            DB::commit();

            return redirect()->route('traslados.index')
                ->with('success', 'Traslado registrado exitosamente');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar el traslado: ' . $e->getMessage());
        }
    }

    /**
     * Process and validate products for traslado
     */
    protected function processProductos(Traslado $traslado, Request $request)
    {
        $productos = $request->get('arrayidproducto', []);
        $cantidades = $request->get('arraycantidad', []);

        // Validar consistencia de datos
        if (count($productos) !== count($cantidades)) {
            throw new Exception('Datos de productos inconsistentes');
        }

        // Agrupar productos por ID para sumar cantidades
        $productosAgrupados = [];
        foreach ($productos as $index => $productoId) {
            $cantidad = floatval($cantidades[$index]);

            if (isset($productosAgrupados[$productoId])) {
                $productosAgrupados[$productoId]['cantidad'] += $cantidad;
            } else {
                $productosAgrupados[$productoId] = [
                    'cantidad' => $cantidad
                ];
            }
        }

        // Procesar productos agrupados
        foreach ($productosAgrupados as $productoId => $detalle) {
            $producto = Producto::findOrFail($productoId);

            // Validar stock en almacén origen
            if ($traslado->origen_almacen_id) {
                $inventarioOrigen = InventarioAlmacen::where('producto_id', $productoId)
                    ->where('almacen_id', $traslado->origen_almacen_id)
                    ->first();

                $stockDisponible = $inventarioOrigen ? $inventarioOrigen->stock : 0;

                if ($stockDisponible < $detalle['cantidad']) {
                    throw new Exception(
                        "Stock insuficiente del producto {$producto->nombre} en almacén origen. " .
                        "Disponible: {$stockDisponible}, Solicitado: {$detalle['cantidad']}"
                    );
                }

                // Decrementar stock del almacén origen
                $inventarioOrigen->decrement('stock', $detalle['cantidad']);
            }

            // Incrementar stock en almacén destino (si existe)
            if ($traslado->destino_almacen_id) {
                $inventarioDestino = InventarioAlmacen::firstOrCreate(
                    [
                        'producto_id' => $productoId,
                        'almacen_id' => $traslado->destino_almacen_id
                    ],
                    ['stock' => 0]
                );

                $inventarioDestino->increment('stock', $detalle['cantidad']);
            }

            // Crear detalle del traslado
            $traslado->detalles()->create([
                'producto_id' => $productoId,
                'cantidad' => $detalle['cantidad']
            ]);
        }
    }

    /**
     * Show the specified resource.
     */
    public function show(Traslado $traslado)
    {
        $traslado->load(['user', 'origenAlmacen', 'destinoAlmacen', 'detalles.producto']);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('traslado.show-modal', compact('traslado'))->render()
            ]);
        }

        return view('traslado.show', compact('traslado'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $traslado = Traslado::with(['user', 'origenAlmacen', 'destinoAlmacen', 'detalles.producto'])->findOrFail($id);

        $almacenes = Almacen::where('estado', 1)
            ->get(['id', 'nombre']);

        // Obtener productos con inventarios
        $productos = Producto::where('estado', 1)
            ->with(['inventarios'])
            ->get(['id', 'codigo', 'nombre']);

        return view('traslado.edit', compact('traslado', 'productos', 'almacenes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrasladoRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            $traslado = Traslado::with('detalles')->findOrFail($id);

            // Revertir stock de los almacenes actuales
            foreach ($traslado->detalles as $detalle) {
                // Revertir del almacén destino anterior
                if ($traslado->destino_almacen_id) {
                    $inventarioDestino = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                        ->where('almacen_id', $traslado->destino_almacen_id)
                        ->first();

                    if ($inventarioDestino) {
                        $inventarioDestino->decrement('stock', (float)$detalle->cantidad);
                    }
                }

                // Restaurar en el almacén origen anterior
                if ($traslado->origen_almacen_id) {
                    $inventarioOrigen = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                        ->where('almacen_id', $traslado->origen_almacen_id)
                        ->first();

                    if ($inventarioOrigen) {
                        $inventarioOrigen->increment('stock', (float)$detalle->cantidad);
                    }
                }
            }

            // Eliminar detalles actuales
            $traslado->detalles()->delete();

            // Actualizar datos del traslado
            $traslado->update([
                'fecha_hora' => $request->fecha_hora,
                'origen_almacen_id' => $request->origen_almacen_id,
                'destino_almacen_id' => $request->destino_almacen_id,
                'costo_envio' => (float)$request->costo_envio,
                'estado' => $request->estado
            ]);

            // Procesar nuevos productos
            $this->processProductos($traslado, $request);

            DB::commit();

            return redirect()->route('traslados.index')
                ->with('success', 'Traslado actualizado exitosamente');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el traslado: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $traslado = Traslado::with('detalles')->findOrFail($id);

                // Revertir stock de los almacenes
                foreach ($traslado->detalles as $detalle) {
                    // Revertir del almacén destino
                    if ($traslado->destino_almacen_id) {
                        $inventarioDestino = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                            ->where('almacen_id', $traslado->destino_almacen_id)
                            ->first();

                        if ($inventarioDestino) {
                            $inventarioDestino->decrement('stock', (float)$detalle->cantidad);
                        }
                    }

                    // Restaurar en el almacén origen
                    if ($traslado->origen_almacen_id) {
                        $inventarioOrigen = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                            ->where('almacen_id', $traslado->origen_almacen_id)
                            ->first();

                        if ($inventarioOrigen) {
                            $inventarioOrigen->increment('stock', (float)$detalle->cantidad);
                        }
                    }
                }

                // Eliminar detalles
                $traslado->detalles()->delete();

                // Eliminar traslado
                $traslado->delete();
            });

            return redirect()->route('traslados.index')
                ->with('success', 'Traslado eliminado permanentemente y stock revertido');
        } catch (Exception $e) {
            return redirect()->route('traslados.index')
                ->with('error', 'No se pudo eliminar el traslado: ' . $e->getMessage());
        }
    }

    /**
     * Update the estado (status) of a traslado
     */
    public function updateEstado(Request $request, string $id)
    {
        try {
            $request->validate([
                'estado' => 'required|in:0,1'
            ]);

            $traslado = Traslado::findOrFail($id);
            $traslado->update(['estado' => $request->estado]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Estado actualizado exitosamente'
                ]);
            }

            return redirect()->route('traslados.index')
                ->with('success', 'Estado actualizado exitosamente');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }

    /**
     * Obtener productos con inventarios para AJAX
     */
    public function getProductos()
    {
        $productos = Producto::where('estado', 1)
            ->with(['inventarios' => function ($q) {
                $q->where('stock', '>', 0);
            }])
            ->get(['id', 'nombre', 'codigo'])
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                    'codigo' => $p->codigo,
                    'inventarios' => $p->inventarios->map(fn($inv) => [
                        'almacen_id' => $inv->almacen_id,
                        'stock' => $inv->stock
                    ])->values()->toArray()
                ];
            });

        return response()->json($productos);
    }

    /**
     * Obtener stock de un producto en un almacén específico
     */
    public function getStockProducto(Request $request)
    {
        $productoId = $request->get('producto_id');
        $almacenId = $request->get('almacen_id');

        $inventario = InventarioAlmacen::where('producto_id', $productoId)
            ->where('almacen_id', $almacenId)
            ->first();

        return response()->json([
            'stock' => $inventario ? $inventario->stock : 0
        ]);
    }

    /**
     * Obtener almacenes válidos (excluyendo uno específico)
     */
    public function getAlmacenes(Request $request)
    {
        $excluidAlmacenId = $request->get('exclude_almacen_id');

        $almacenes = Almacen::where('estado', 1)
            ->when($excluidAlmacenId, function ($q) use ($excluidAlmacenId) {
                $q->where('id', '!=', $excluidAlmacenId);
            })
            ->get(['id', 'nombre']);

        return response()->json($almacenes);
    }
}
