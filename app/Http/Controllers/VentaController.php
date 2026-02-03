<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Http\Requests\UpdateVentaRequest;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Comprobante;
use App\Models\InventarioAlmacen;
use App\Traits\FilterByAlmacen;
use Exception;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Services\VentaService;

class VentaController extends Controller
{
    use FilterByAlmacen;
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
            'detalles.producto',
            'almacen'
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

        $producto = Producto::with('tipounidad')->find($request->producto_id);

        if ($producto->tipounidad && !$producto->tipounidad->maneja_stock) {
            return response()->json([
                'success' => true,
                'stock' => 999999, // Un numero alto para indicar ilimitado
                'ilimitado' => true
            ]);
        }

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

            // Validar stock de forma masiva
            $items = [];
            foreach ($request->arrayidproducto as $index => $productoId) {
                $items[] = [
                    'producto_id' => $productoId,
                    'cantidad' => floatval($request->arraycantidad[$index] ?? 0)
                ];
            }
            $this->ventaService->validarStockDisponible($items, $request->almacen_id);

            $venta = Venta::create([
                'fecha_hora' => $request->fecha_hora ?? now(),
                'numero_comprobante' => $request->numero_comprobante,
                'total' => 0,
                'estado_pago' => 'pendiente',
                'estado_entrega' => 'por_entregar',
                'cliente_id' => $request->cliente_id,
                'almacen_id' => $request->almacen_id,
                'comprobante_id' => $request->comprobante_id,
                'user_id' => auth()->id(),
                'nota_personal' => $request->nota_personal ?? null,
                'nota_cliente' => $request->nota_cliente ?? null,
            ]);

            $total = 0;
            foreach ($request->arrayidproducto as $index => $productoId) {
                $cantidad = floatval($request->arraycantidad[$index] ?? 0);
                $precioVenta = floatval($request->arrayprecioventa[$index] ?? 0);
                $descuento = floatval($request->arraydescuento[$index] ?? 0);

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
        $venta->load(['comprobante', 'cliente.persona', 'user', 'detalles.producto.tipounidad', 'almacen']);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'venta' => $venta,
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
            'detalles.producto.tipounidad'
        ])->findOrFail($id);

        $almacenes = \App\Models\Almacen::where('estado', 1)->get();
        $comprobantes = Comprobante::all();

        $productos = Producto::where('estado', 1)->get();

        $clientes = Cliente::with(['persona' => function ($query) {
            $query->where('estado', 1);
        }])->get();

        return view('venta.edit', compact('venta', 'productos', 'clientes', 'comprobantes', 'almacenes'));
    }

    public function update(UpdateVentaRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            // Cargar con relaciones para el service
            $venta = Venta::with('detalles.producto.tipounidad')->findOrFail($id);

            // 1. Revertir stock (del almacén que tenía la venta originalmente)
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
                'nota_cliente' => $request->nota_cliente ?? null,
            ]);

            // 4. Validar nuevo stock disponible (en el nuevo almacén)
            $items = [];
            foreach ($request->arrayidproducto as $index => $productoId) {
                $items[] = [
                    'producto_id' => $productoId,
                    'cantidad' => floatval($request->arraycantidad[$index] ?? 0)
                ];
            }
            $this->ventaService->validarStockDisponible($items, $request->almacen_id);

            // 5. Crear nuevos detalles
            $total = 0;
            foreach ($request->arrayidproducto as $index => $productoId) {
                $cantidad = floatval($request->arraycantidad[$index] ?? 0);
                $precioVenta = floatval($request->arrayprecioventa[$index] ?? 0);
                $descuento = floatval($request->arraydescuento[$index] ?? 0);

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
            $venta->update(['total' => $total]);

            // 6. Salida stock nuevamente (asegurarse de usar detalles recién creados)
            $venta->refresh();
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
            DB::beginTransaction();

            $venta = Venta::with('detalles.producto.tipounidad')->findOrFail($id);

            // Revertir stock antes de borrar
            $this->ventaService->revertirStock($venta);

            // Borrar físicamente (o podrías marcar estado = 0 si prefieres)
            $venta->detalles()->delete();
            $venta->delete();

            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', 'Venta eliminada y stock revertido correctamente');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('ventas.index')
                ->with('error', 'No se pudo eliminar la venta: ' . $e->getMessage());
        }
    }

    public function actualizarEstadoPago(Request $request, string $id)
    {
        try {
            $venta = Venta::findOrFail($id);

            $estado = $request->input('estado_pago', 'pagado');

            $venta->update(['estado_pago' => $estado]);

            return response()->json([
                'success' => true,
                'message' => 'Estado de pago actualizado correctamente',
                'venta' => $venta
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado de pago: ' . $e->getMessage()
            ], 400);
        }
    }

    public function actualizarEstadoEntrega(Request $request, string $id)
    {
        try {
            $venta = Venta::findOrFail($id);

            $estado = $request->input('estado_entrega', 'entregado');

            $venta->update(['estado_entrega' => $estado]);

            return response()->json([
                'success' => true,
                'message' => 'Estado de entrega actualizado correctamente',
                'venta' => $venta
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado de entrega: ' . $e->getMessage()
            ], 400);
        }
    }
}
