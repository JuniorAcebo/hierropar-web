<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVentaRequest;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use Exception;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-venta|crear-venta|mostrar-venta|eliminar-venta', ['only' => ['index']]);
        $this->middleware('permission:crear-venta', ['only' => ['create', 'store']]);
        $this->middleware('permission:mostrar-venta', ['only' => ['show']]);
        $this->middleware('permission:editar-venta', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-venta', ['only' => ['destroy']]);
    }

    public function index()
    {
        $ventas = Venta::with([
            'comprobante',
            'cliente.persona', // Esta carga eager está bien
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
            'productos' => fn($q) => $q->withPivot('cantidad', 'precio_venta', 'descuento')
        ])->findOrFail($id);

        $pdf = Pdf::loadView('venta.pdf', compact('venta'))
            ->setPaper('a4', 'portrait');

        $fileName = "VENTA-{$venta->numero_comprobante}-{$venta->cliente->persona->nombre}.pdf";

        if ($request->has('print')) {
            return $pdf->stream($fileName);
        }

        return $pdf->download($fileName);
    }

    public function create()
    {
        $productos = Producto::where('estado', 1)
            ->where('stock', '>', 0)
            ->get(['id', 'codigo', 'nombre', 'stock', 'precio_compra', 'precio_venta']);

        $clientes = Cliente::with('persona')
            ->whereHas('persona', fn($q) => $q->where('estado', 1))
            ->get();

        $nextComprobanteNumber = $this->getNextComprobanteNumber();

        return view('venta.create', compact('productos', 'clientes', 'comprobantes', 'nextComprobanteNumber'));
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

            $ventaData = $request->validated();
            $ventaData['user_id'] = auth()->id();
            $ventaData['fecha_hora'] = now();

            $venta = Venta::create($ventaData);

            $this->processProductos($venta, $request);

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

    protected function processProductos(Venta $venta, Request $request)
    {
        $productos = $request->get('arrayidproducto', []);
        $cantidades = $request->get('arraycantidad', []);
        $preciosVenta = $request->get('arrayprecioventa', []);
        $descuentos = $request->get('arraydescuento', []);

        if (
            count($productos) !== count($cantidades) ||
            count($productos) !== count($preciosVenta) ||
            count($productos) !== count($descuentos)
        ) {
            throw new Exception('Datos de productos inconsistentes');
        }

        foreach ($productos as $index => $productoId) {
            $cantidad = (float) $cantidades[$index];
            $precioVenta = (float) $preciosVenta[$index];
            $descuento = (float) ($descuentos[$index] ?? 0);

            $producto = Producto::findOrFail($productoId);
            $compra = $producto->compras()->latest()->first();
            $compraId = $compra ? $compra->id : null;

            if ($producto->stock < $cantidad) {
                throw new Exception("Stock insuficiente para el producto: {$producto->nombre}");
            }

            $venta->productos()->attach($productoId, [
                'cantidad' => $cantidad,
                'precio_venta' => $precioVenta,
                'descuento' => $descuento,
                'compra_id' => $compraId
            ]);

            $producto->decrement('stock', $cantidad);
        }
    }

    public function show(Venta $venta)
    {
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
            'productos' => fn($q) => $q->withPivot('cantidad', 'precio_venta', 'descuento')
        ])->findOrFail($id);

        $productos = Producto::where('estado', 1)
            ->where(function ($query) use ($venta) {
                $query->where('stock', '>', 0)
                    ->orWhereIn('id', $venta->productos->pluck('id'));
            })
            ->get(['id', 'codigo', 'nombre', 'stock', 'precio_venta']);

        // 🔹 Ajustar el stock sumando la cantidad de la venta
        foreach ($venta->productos as $detalle) {
            $producto = $productos->firstWhere('id', $detalle->id);
            if ($producto) {
                $producto->stock += $detalle->pivot->cantidad;
            }
        }

        $clientes = Cliente::with(['persona' => function ($query) {
            $query->where('estado', 1);
        }])->get();


        return view('venta.edit', compact('venta', 'productos', 'clientes', 'comprobantes'));
    }


    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $venta = Venta::with('productos')->findOrFail($id);

            foreach ($venta->productos as $producto) {
                $producto->increment('stock', (float) $producto->pivot->cantidad);
            }

            $venta->productos()->detach();

            $venta->update([
                'numero_comprobante' => $request->numero_comprobante,
                'fecha_hora' => $request->fecha_hora ?? now(),
                'total' => (float) $request->total,
                'comprobante_id' => $request->comprobante_id,
                'cliente_id' => $request->cliente_id
            ]);

            $this->processProductos($venta, $request);

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
                $venta = Venta::with('productos')->findOrFail($id);

                foreach ($venta->productos as $producto) {
                    $producto->increment('stock', (float) $producto->pivot->cantidad);
                }

                $venta->productos()->detach();
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
