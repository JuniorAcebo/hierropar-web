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
use Illuminate\Support\Facades\URL;
use App\Models\VentaPago;

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
        $metodoPago = $request->get('metodo_pago');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $perPage  = $request->get('per_page', 10);
        $sort     = $request->get('sort', 'fecha_hora');
        $direction = $request->get('direction', 'desc');

        // Validar per_page y direction
        if (!in_array($perPage, [5, 10, 15, 20, 25])) $perPage = 10;
        if (!in_array($direction, ['asc', 'desc'])) $direction = 'desc';

        $query = Venta::with(['comprobante', 'cliente.persona', 'user', 'pagos'])
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

        if ($metodoPago) {
            $query->whereHas('pagos', function ($q) use ($metodoPago) {
                $q->where('metodo_pago', $metodoPago);
            });
        }
        if ($dateFrom) {
            $query->whereDate('fecha_hora', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('fecha_hora', '<=', $dateTo);
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
        if ($metodoPago) {
            $statsQuery->whereHas('pagos', function ($q) use ($metodoPago) {
                $q->where('metodo_pago', $metodoPago);
            });
        }
        if ($dateFrom) $statsQuery->whereDate('fecha_hora', '>=', $dateFrom);
        if ($dateTo) $statsQuery->whereDate('fecha_hora', '<=', $dateTo);

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
                'metodoPago',
                'dateFrom',
                'dateTo',
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
            'metodoPago',
            'dateFrom',
            'dateTo',
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

    public function facturaPublica(Venta $venta, Request $request)
    {
        $venta->load([
            'comprobante',
            'cliente.persona',
            'user',
            'detalles.producto',
            'almacen'
        ]);

        $pdf = Pdf::loadview('admin.venta.pdf', compact('venta'))
            ->setPaper('a4', 'portrait');

        $fileName = "VENTA-{$venta->numero_comprobante}.pdf";

        if ($request->boolean('download')) {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }

    public function create()
    {
        // Obtener productos activos con información básica
        $productos = Producto::with('tipounidad:id,nombre,maneja_stock')
            ->where('estado', 1)
            ->get(['id', 'codigo', 'nombre', 'precio_compra', 'precio_venta', 'tipounidad_id']);

        $clientes = Cliente::with(['persona' => function ($query) {
            $query->where('estado', 1);
        }, 'grupo'])->get();

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

            $numeroComprobante = $request->numero_comprobante;
            if (empty($numeroComprobante)) {
                $numeroComprobante = $this->getNextComprobanteNumber();
            }

            $data = $request->validated();
            $data['numero_comprobante'] = $numeroComprobante;

            $this->ventaService->crearVenta($data, auth()->id());

            return redirect()->route('ventas.index')
                ->with('success', 'Venta registrada exitosamente');
        } catch (Exception $e) {
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

        $venta->load(['comprobante', 'cliente.persona', 'user', 'detalles.producto.tipounidad', 'almacen', 'pagos']);

        if (request()->ajax() || request()->wantsJson()) {
            $telefono = optional(optional($venta->cliente)->persona)->telefono;
            $pdfUrl = route('ventas.pdf', ['id' => $venta->id]);
            $facturaUrl = URL::temporarySignedRoute(
                'facturas.ventas',
                now()->addDays(30),
                ['venta' => $venta->id]
            );

            return response()->json([
                'success' => true,
                'venta' => $venta,
                'telefono' => $telefono,
                'pdf_url' => $pdfUrl,
                'factura_url' => $facturaUrl,
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
            'detalles.producto.tipounidad',
            'pagos'
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
        }, 'grupo'])->get();

        return view('admin.venta.edit', compact('venta', 'productos', 'clientes', 'comprobantes', 'almacenes'));
    }

    public function update(UpdateVentaRequest $request, string $id)
    {
        try {
            $venta = Venta::findOrFail($id);
            if (!$this->canAccessAlmacen($venta->almacen_id) || !$this->canAccessAlmacen($request->almacen_id)) {
                return redirect()->back()->withInput()->with('error', 'Acceso denegado al almacen.');
            }

            $this->ventaService->actualizarVenta($venta, $request->validated());

            return redirect()->route('ventas.index')
                ->with('success', 'Venta actualizada exitosamente');
        } catch (Exception $e) {
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
            $venta = Venta::with('pagos')->findOrFail($id);

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

            if (in_array($estadoNuevo, ['cancelado', 'anulado', 'pendiente'])) {
                $venta->pagos()->delete();
            } elseif ($estadoNuevo === 'parcial') {
                $metodo = $request->input('metodo_pago', 'efectivo');
                $monto = (float) $request->input('monto_pagado', 0);
                $monto = max(0.0, min($monto, (float) $venta->saldo));
                if ($monto > 0) {
                    $venta->pagos()->create(['metodo_pago' => $metodo, 'monto' => $monto]);
                }
            } elseif ($estadoNuevo === 'pagado') {
                $metodo = $request->input('metodo_pago', 'efectivo');
                $saldo = (float) $venta->saldo;
                if ($saldo > 0) {
                    $venta->pagos()->create(['metodo_pago' => $metodo, 'monto' => $saldo]);
                }
            }

            $venta->refresh();
            $montoPagado = (float) $venta->pagos()->sum('monto');
            $estadoPago = $montoPagado >= (float) $venta->total ? 'pagado' : ($montoPagado > 0 ? 'parcial' : 'pendiente');
            $metodoPago = $venta->pagos()->distinct()->count('metodo_pago') > 1
                ? 'mixto'
                : ($venta->pagos()->value('metodo_pago') ?? 'efectivo');

            $venta->update([
                'estado_pago' => in_array($estadoNuevo, ['cancelado', 'anulado']) ? $estadoNuevo : $estadoPago,
                'monto_pagado' => $montoPagado,
                'metodo_pago' => $metodoPago,
            ]);

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

    public function reporteCaja(Request $request)
    {
        $dateFrom = $request->get('date_from') ?: now()->toDateString();
        $dateTo = $request->get('date_to') ?: now()->toDateString();

        $userAlmacenId = auth()->user()->almacen_id;

        $base = VentaPago::query()
            ->join('ventas', 'venta_pagos.venta_id', '=', 'ventas.id')
            ->leftJoin('users', 'ventas.user_id', '=', 'users.id')
            ->where('ventas.estado', 1)
            ->whereNotIn('ventas.estado_pago', ['cancelado', 'anulado'])
            ->whereDate('ventas.fecha_hora', '>=', $dateFrom)
            ->whereDate('ventas.fecha_hora', '<=', $dateTo);

        if ($userAlmacenId) {
            $base->where('ventas.almacen_id', $userAlmacenId);
        }

        $rows = $base->selectRaw("ventas.user_id, COALESCE(users.name,'(Sin usuario)') as usuario, venta_pagos.metodo_pago, SUM(venta_pagos.monto) as monto")
            ->groupBy('ventas.user_id', 'users.name', 'venta_pagos.metodo_pago')
            ->orderBy('usuario')
            ->get();

        $report = [];
        foreach ($rows as $r) {
            $uid = $r->user_id ?? 0;
            if (!isset($report[$uid])) {
                $report[$uid] = [
                    'user_id' => $uid,
                    'usuario' => $r->usuario,
                    'efectivo' => 0.0,
                    'qr' => 0.0,
                    'debito' => 0.0,
                    'deposito' => 0.0,
                    'otro' => 0.0,
                ];
            }
            $metodo = $r->metodo_pago;
            if (isset($report[$uid][$metodo])) {
                $report[$uid][$metodo] += (float) $r->monto;
            }
        }

        $report = array_values(array_map(function ($row) {
            $row['total'] = $row['efectivo'] + $row['qr'] + $row['debito'] + $row['deposito'] + $row['otro'];
            $row['debe_recoger_caja'] = $row['efectivo'];
            return $row;
        }, $report));

        $totales = [
            'efectivo' => array_sum(array_column($report, 'efectivo')),
            'qr' => array_sum(array_column($report, 'qr')),
            'debito' => array_sum(array_column($report, 'debito')),
            'deposito' => array_sum(array_column($report, 'deposito')),
            'otro' => array_sum(array_column($report, 'otro')),
        ];
        $totales['total'] = $totales['efectivo'] + $totales['qr'] + $totales['debito'] + $totales['deposito'] + $totales['otro'];
        $totales['debe_recoger_caja'] = $totales['efectivo'];

        return view('admin.venta.reporte-caja', compact('report', 'totales', 'dateFrom', 'dateTo'));
    }
}
