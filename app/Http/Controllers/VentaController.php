<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Comprobante;
use App\Models\InventarioAlmacen;
use Exception;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Services\VentaService;

class VentaController extends Controller
{
    protected $ventaService;

    function __construct(VentaService $ventaService)
    {
        $this->ventaService = $ventaService;
        $this->middleware('permission:ver-venta|crear-venta|mostrar-venta|eliminar-venta', ['only' => ['index', 'show']]);
        $this->middleware('permission:crear-venta', ['only' => ['create', 'store']]);
        $this->middleware('permission:mostrar-venta', ['only' => ['show']]);
        $this->middleware('permission:editar-venta', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-venta', ['only' => ['destroy']]);
    }

    public function index()
    {
        $ventas = Venta::with([
            'comprobante',
            'cliente.persona',
            'user'
        ])
            ->where('estado', 1)
            ->latest()
            ->get();

        return view('venta.index', compact('ventas'));
    }

    public function generarPdf($id, Request $request)
    {
        $venta = Venta::with([
            'comprobante',
            'cliente.persona',
            'user',
            'detalles.producto'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('venta.pdf', compact('venta'))
            ->setPaper('a4', 'portrait');

        $fileName = "VENTA-{$venta->numero_comprobante}-{$venta->cliente->persona->razon_social}.pdf";

        if ($request->has('print')) {
            return $pdf->stream($fileName);
        }

        return $pdf->download($fileName);
    }

    public function create()
    {
        // Obtener productos activos con información básica
        $productos = Producto::where('estado', 1)
            ->get(['id', 'codigo', 'nombre', 'precio_compra', 'precio_venta']);

        $clientes = Cliente::with(['persona' => function ($query) {
            $query->where('estado', 1);
        }])->get();

        $comprobantes = Comprobante::all();
        $almacenes = \App\Models\Almacen::where('estado', 1)->get();
        $nextComprobanteNumber = $this->getNextComprobanteNumber();

        return view('venta.create', compact('productos', 'clientes', 'comprobantes', 'almacenes', 'nextComprobanteNumber'));
    }

    // Nuevo método para consultar stock por almacén
    public function checkStock(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'almacen_id' => 'required|exists:almacenes,id'
        ]);

        $inventario = InventarioAlmacen::where('producto_id', $request->producto_id)
            ->where('almacen_id', $request->almacen_id)
            ->first();

        $stock = $inventario ? $inventario->stock : 0;

        return response()->json([
            'success' => true,
            'stock' => (float) $stock
        ]);
    }

    protected function getNextComprobanteNumber()
    {
        $lastVenta = Venta::latest()->first();
        return $lastVenta ? (int)$lastVenta->numero_comprobante + 1 : 1;
    }

    public function store(StoreVentaRequest $request)
    {
        try {
            DB::beginTransaction();
            
            // Validar stock antes de crear nada
            $items = [];
            foreach ($request->arrayidproducto as $index => $productoId) {
                $items[] = [
                    'producto_id' => $productoId,
                    'cantidad' => $request->arraycantidad[$index]
                ];
            }
            $this->ventaService->validarStockDisponible($items, $request->almacen_id);

            $venta = Venta::create([
                'fecha_hora' => now(),
                'numero_comprobante' => $request->numero_comprobante,
                'total' => 0,
                'estado_pago' => 'pendiente',
                'estado_entrega' => 'por_entregar',
                'cliente_id' => $request->cliente_id,
                'almacen_id' => $request->almacen_id,
                'comprobante_id' => $request->comprobante_id,
                'user_id' => auth()->id(),
                'nota_personal' => $request->nota_personal ?? null,
            ]);

            $total = 0;
            foreach ($request->arrayidproducto as $index => $productoId) {
                $cantidad = $request->arraycantidad[$index];
                $precioVenta = $request->arrayprecioventa[$index];
                $descuento = $request->arraydescuento[$index] ?? 0;

                $venta->detalles()->create([
                    'producto_id' => $productoId,
                    'cantidad' => $cantidad,
                    'precio_venta' => $precioVenta,
                    'descuento' => $descuento
                ]);

                $total += ($cantidad * $precioVenta) - $descuento;
            }

            $venta->update(['total' => $total]);

            // Procesar salida de stock
            $this->ventaService->procesarSalidaStock($venta);

            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', 'Venta registrada exitosamente');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar la venta: ' . $e->getMessage());
        }
    }

    public function show(Venta $venta)
    {
        $venta->load(['comprobante', 'cliente.persona', 'user', 'detalles.producto', 'almacen']);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('venta.show-modal', compact('venta'))->render()
            ]);
        }

        return view('venta.show', compact('venta'));
    }

    public function edit(string $id)
    {
        $venta = Venta::with([
            'comprobante',
            'cliente.persona',
            'detalles.producto'
        ])->findOrFail($id);

        $almacenes = \App\Models\Almacen::where('estado', 1)->get();
        $comprobantes = Comprobante::all();
        
        $productos = Producto::where('estado', 1)->get();
        
        $clientes = Cliente::with(['persona' => function ($query) {
            $query->where('estado', 1);
        }])->get();

        return view('venta.edit', compact('venta', 'productos', 'clientes', 'comprobantes', 'almacenes'));
    }

    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $venta = Venta::with('detalles')->findOrFail($id);

            // 1. Revertir stock (devolver entrada)
            $this->ventaService->revertirStock($venta);

            // 2. Eliminar detalles viejos
            $venta->detalles()->delete();

            // 3. Update Venta info
            $venta->update([
                'numero_comprobante' => $request->numero_comprobante,
                'fecha_hora' => $request->fecha_hora ?? now(),
                'cliente_id' => $request->cliente_id,
                'almacen_id' => $request->almacen_id,
                'comprobante_id' => $request->comprobante_id,
                'nota_personal' => $request->nota_personal ?? null,
            ]);

            // 4. Create new details
            $total = 0;
            $items = [];
            foreach ($request->arrayidproducto as $index => $productoId) {
                $items[] = [
                    'producto_id' => $productoId,
                    'cantidad' => $request->arraycantidad[$index]
                ];
            }
            
            $this->ventaService->validarStockDisponible($items, $request->almacen_id);

            foreach ($request->arrayidproducto as $index => $productoId) {
                $cantidad = $request->arraycantidad[$index];
                $precioVenta = $request->arrayprecioventa[$index];
                $descuento = $request->arraydescuento[$index] ?? 0;

                $venta->detalles()->create([
                    'producto_id' => $productoId,
                    'cantidad' => $cantidad,
                    'precio_venta' => $precioVenta,
                    'descuento' => $descuento
                ]);
                
               $total += ($cantidad * $precioVenta) - $descuento;
            }
            $venta->update(['total' => $total]);

            // 5. Salida stock nuevamente
            $this->ventaService->procesarSalidaStock($venta);

            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', 'Venta actualizada exitosamente');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la venta: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $venta = Venta::with('detalles')->findOrFail($id);

                // Revert stock
                $this->ventaService->revertirStock($venta);

                // Delete details and sale
                $venta->detalles()->delete();
                $venta->delete();
            });

            return redirect()->route('ventas.index')
                ->with('success', 'Venta eliminada permanentemente y stock revertido');
        } catch (Exception $e) {
            return redirect()->route('ventas.index')
                ->with('error', 'No se pudo eliminar la venta: ' . $e->getMessage());
        }
    }
}