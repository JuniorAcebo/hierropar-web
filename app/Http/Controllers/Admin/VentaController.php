<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    public function index(Request $request)
    {
        $busqueda = $request->get('busqueda');
        $perPage  = $request->get('per_page', 10);
        $sort     = $request->get('sort', 'fecha_hora');
        $direction = $request->get('direction', 'desc');

        // Validar per_page y direction
        if (!in_array($perPage, [5, 10, 15, 20, 25])) $perPage = 10;
        if (!in_array($direction, ['asc', 'desc'])) $direction = 'desc';

        $query = Venta::with(['comprobante', 'cliente.persona', 'user'])
            ->where('estado', 1);

        // Filtrar por almacén del usuario
        $query = $this->filterByUserAlmacen($query);

        // Búsqueda
        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('numero_comprobante', 'like', "%{$busqueda}%")
                    ->orWhereHas('cliente.persona', function ($pq) use ($busqueda) {
                        $pq->where('razon_social', 'like', "%{$busqueda}%")
                            ->orWhere('numero_documento', 'like', "%{$busqueda}%");
                    })
                    ->orWhereHas('comprobante', function ($cq) use ($busqueda) {
                        $cq->where('tipo_comprobante', 'like', "%{$busqueda}%");
                    });
            });
        }

        // Ordenamiento
        switch ($sort) {
            case 'cliente':
                $query->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
                    ->join('personas', 'clientes.persona_id', '=', 'personas.id')
                    ->select('ventas.*')
                    ->orderBy('personas.razon_social', $direction);
                break;
            case 'numero_comprobante':
            case 'total':
            case 'fecha_hora':
            case 'estado_pago':
            case 'estado_entrega':
                $query->orderBy($sort, $direction);
                break;
            default:
                $query->latest('fecha_hora');
                break;
        }

        $ventas = $query->paginate($perPage);

        // Estadísticas para el footer - Filtradas por almacen
        $statsQuery = Venta::where('estado', 1);
        $statsQuery = $this->filterByUserAlmacen($statsQuery);

        $totalVentasMonto = (clone $statsQuery)->sum('total');
        $ventasPagadas = (clone $statsQuery)
            ->where(function ($q) {
                $q->where('estado_pago', '!=', 'pendiente')
                    ->where('estado_pago', '!=', 0);
            })->count();
        $ventasPendientesPago = (clone $statsQuery)
            ->where(function ($q) {
                $q->where('estado_pago', 'pendiente')
                    ->orWhere('estado_pago', 0);
            })->count();

        if ($request->ajax()) {
            return view('admin.venta.index', compact(
                'ventas',
                'busqueda',
                'perPage',
                'sort',
                'direction',
                'totalVentasMonto',
                'ventasPagadas',
                'ventasPendientesPago'
            ));
        }

        return view('admin.venta.index', compact(
            'ventas',
            'busqueda',
            'perPage',
            'sort',
            'direction',
            'totalVentasMonto',
            'ventasPagadas',
            'ventasPendientesPago'
        ));
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

        $pdf = Pdf::loadview('admin.venta.pdf', compact('venta'))
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
        
        // Filtrar almacenes disponibles para el usuario
        $userAlmacenId = auth()->user()->almacen_id;
        if ($userAlmacenId) {
            $almacenes = \App\Models\Almacen::where('estado', 1)->where('id', $userAlmacenId)->get();
        } else {
            $almacenes = \App\Models\Almacen::where('estado', 1)->get();
        }
        $nextComprobanteNumber = $this->getNextComprobanteNumber();

        return view('admin.venta.create', compact('productos', 'clientes', 'comprobantes', 'almacenes', 'nextComprobanteNumber'));
    }

    // Nuevo método para consultar stock por almacén
    public function checkStock(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'almacen_id' => 'required|exists:almacenes,id'
        ]);

        if (!$this->canAccessAlmacen($request->almacen_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado a este almacén.'
            ], 403);
        }

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
        $nextNumber = $lastVenta ? (int)$lastVenta->numero_comprobante + 1 : 1;
        return str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
    }

    public function store(StoreVentaRequest $request)
    {
        try {
            // Validar si el usuario tiene permiso para este almacén
            if (!$this->canAccessAlmacen($request->almacen_id)) {
                return redirect()->back()->withInput()->with('error', 'No tiene permiso para realizar ventas en este almacén.');
            }

            DB::beginTransaction();

            // Validar stock de forma masiva
            $items = [];
            foreach ($request->arrayidproducto as $index => $productoId) {
                $items[] = [
                    'producto_id' => $productoId,
                    'cantidad' => $request->arraycantidad[$index]
                ];
            }
            $this->ventaService->validarStockDisponible($items, $request->almacen_id);

            $numeroComprobante = $request->numero_comprobante;
            if (empty($numeroComprobante)) {
                $numeroComprobante = $this->getNextComprobanteNumber();
            }

            $venta = Venta::create([
                'fecha_hora' => $request->fecha_hora ?? now(),
                'numero_comprobante' => $numeroComprobante,
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
        if (!$this->canAccessAlmacen($venta->almacen_id)) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Acceso denegado'], 403);
            }
            return redirect()->route('ventas.index')->with('error', 'No tiene acceso a esta venta.');
        }

        $venta->load(['comprobante', 'cliente.persona', 'user', 'detalles.producto.tipounidad', 'almacen']);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'venta' => $venta,
                'html' => view('admin.venta.show-modal', compact('venta'))->render()
            ]);
        }

        return view('admin.venta.show', compact('venta'));
    }

    public function edit(string $id)
    {
        $venta = Venta::with([
            'comprobante',
            'cliente.persona',
            'detalles.producto.tipounidad'
        ])->findOrFail($id);

        if (!$this->canAccessAlmacen($venta->almacen_id)) {
            return redirect()->route('ventas.index')->with('error', 'No tiene acceso a esta venta.');
        }

        $comprobantes = Comprobante::all();
        
        // Filtrar almacenes disponibles para el usuario
        $userAlmacenId = auth()->user()->almacen_id;
        if ($userAlmacenId) {
            $almacenes = \App\Models\Almacen::where('estado', 1)->where('id', $userAlmacenId)->get();
        } else {
            $almacenes = \App\Models\Almacen::where('estado', 1)->get();
        }

        $productos = Producto::where('estado', 1)->get();

        $clientes = Cliente::with(['persona' => function ($query) {
            $query->where('estado', 1);
        }])->get();

        return view('admin.venta.edit', compact('venta', 'productos', 'clientes', 'comprobantes', 'almacenes'));
    }

    public function update(UpdateVentaRequest $request, string $id)
    {
        try {
            // Validar acceso al almacen original y al nuevo
            $venta = Venta::findOrFail($id);
            if (!$this->canAccessAlmacen($venta->almacen_id) || !$this->canAccessAlmacen($request->almacen_id)) {
                return redirect()->back()->withInput()->with('error', 'Acceso denegado al almacen.');
            }

            DB::beginTransaction();

            // Cargar con relaciones para el service
            $venta = Venta::with('detalles.producto.tipounidad')->findOrFail($id);

            // 1. Revertir stock (del almacen que tenía la venta originalmente)
            $this->ventaService->revertirStock($venta);

            // 2. Eliminar detalles viejos
            $venta->detalles()->delete();

            // 3. Update Venta info
            $venta->update([
                'numero_comprobante' => $request->numero_comprobante ?? $venta->numero_comprobante,
                'fecha_hora' => $request->fecha_hora ?? now(),
                'cliente_id' => $request->cliente_id,
                'almacen_id' => $request->almacen_id,
                'comprobante_id' => $request->comprobante_id,
                'nota_personal' => $request->nota_personal ?? null,
                'nota_cliente' => $request->nota_cliente ?? null,
            ]);

            // 4. Validar nuevo stock disponible (en el nuevo almacen)
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
            $venta = Venta::with('detalles.producto.tipounidad')->findOrFail($id);

            if (!$this->canAccessAlmacen($venta->almacen_id)) {
                return redirect()->route('ventas.index')->with('error', 'No tiene acceso para eliminar esta venta.');
            }

            DB::beginTransaction();

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

            if (!$this->canAccessAlmacen($venta->almacen_id)) {
                return response()->json(['success' => false, 'message' => 'Acceso denegado'], 403);
            }

            $estadoAnterior = $venta->estado_pago;
            $estadoNuevo = $request->input('estado_pago', 'pagado');

            DB::beginTransaction();

            // Si se cancela/anula y no estaba cancelada antes -> Revertir stock
            if (in_array($estadoNuevo, ['cancelado', 'anulado']) && !in_array($estadoAnterior, ['cancelado', 'anulado'])) {
                $this->ventaService->revertirStock($venta);
            }
            // Si se recupera de cancelado -> Procesar salida stock nuevamente
            elseif (!in_array($estadoNuevo, ['cancelado', 'anulado']) && in_array($estadoAnterior, ['cancelado', 'anulado'])) {
                $this->ventaService->procesarSalidaStock($venta);
            }

            $venta->update(['estado_pago' => $estadoNuevo]);

            DB::commit();

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