<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Http\Requests\UpdateCompraRequest;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Comprobante;
use App\Models\DetalleVenta;
use App\Models\Proveedor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class CompraController extends Controller
{

    public function generarPdf($id, Request $request)
    {
        //Call to undefined relationship [proveedore] on model [App\Models\Compra].
        
        $compra = Compra::with([
            'comprobante',
            'proveedor.persona',
            'detalles.producto',
            'almacen',
            'user'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('compra.pdf', compact('compra'))
            ->setPaper('a4', 'portrait');

        $fileName = "COMPRA-{$compra->numero_comprobante}-{$compra->proveedor->persona->razon_social}.pdf";

        if ($request->has('print')) {
            return $pdf->stream($fileName);
        }

        return $pdf->download($fileName);
    }

    protected $compraService;

    function __construct(\App\Services\CompraService $compraService)
    {
        $this->compraService = $compraService;
        $this->middleware('permission:ver-compra|crear-compra|mostrar-compra|eliminar-compra', ['only' => ['index']]);
        $this->middleware('permission:crear-compra', ['only' => ['create', 'store']]);
        $this->middleware('permission:mostrar-compra', ['only' => ['show']]);
        $this->middleware('permission:editar-compra', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-compra', ['only' => ['destroy']]);
    }

    public function index()
    {
        $compras = Compra::with(['comprobante', 'proveedor.persona', 'user'])
            ->where('estado', 1)
            ->latest()
            ->get();

        return view('compra.index', compact('compras'));
    }


    public function create()
    {
        $proveedores = Proveedor::with('persona')->get();
        $comprobantes = Comprobante::all();
        $almacenes = \App\Models\Almacen::where('estado', 1)->get();
        $productos = Producto::where('estado', 1)->get();
        
        // Generar número de comprobante automático
        $ultimaCompra = Compra::latest('id')->first();
        $nextNumber = $ultimaCompra ? (intval($ultimaCompra->numero_comprobante) + 1) : 1;
        $nextComprobanteNumber = str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
        
        return view('compra.create', compact('proveedores', 'comprobantes', 'almacenes', 'productos', 'nextComprobanteNumber'));
    }

    public function store(StoreCompraRequest $request)
    {
        try {
            DB::beginTransaction();

            $compra = Compra::create([
                'fecha_hora' => now(),
                'numero_comprobante' => $request->numero_comprobante,
                'total' => 0, // Se calculará abajo
                'costo_transporte' => $request->costo_transporte ?? 0,
                'nota_personal' => $request->nota_personal,
                'estado_pago' => $request->estado_pago ?? 'pendiente',
                'estado_entrega' => $request->estado_entrega ?? 'por_entregar',
                'comprobante_id' => $request->comprobante_id,
                'proveedor_id' => $request->proveedor_id,
                'almacen_id' => $request->almacen_id,
                'user_id' => auth()->id(),
            ]);

            $total = 0;
            foreach ($request->arrayidproducto as $index => $productoId) {
                $cantidad = $request->arraycantidad[$index];
                $precioCompra = $request->arraypreciocompra[$index];
                $precioVenta = $request->arrayprecioventa[$index];

                $compra->detalles()->create([
                    'producto_id' => $productoId,
                    'cantidad' => $cantidad,
                    'precio_compra' => $precioCompra,
                    'precio_venta' => $precioVenta,
                ]);

                $total += ($cantidad * $precioCompra);
            }

            $compra->update(['total' => $total]);

            // Procesar stock
            $this->compraService->procesarEntradaStock($compra);

            DB::commit();
            return redirect()->route('compras.index')->with('success', 'Compra registrada exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al registrar: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    // En CompraController.php
    public function show(Compra $compra)
    {
        $compra->load(['comprobante', 'proveedor.persona', 'detalles.producto', 'almacen', 'user']);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('compra.show-modal', compact('compra'))->render()
            ]);
        }

        return view('compra.show', compact('compra'));
    }

    public function edit(Compra $compra)
    {
        $compra->load(['detalles.producto', 'proveedor.persona']);
        
        $productosInfo = [];
        foreach ($compra->detalles as $detalle) {
            $producto = $detalle->producto;
            // Calculamos cuánto se ha vendido de este producto en total
            //$vendido = \App\Models\DetalleVenta::where('producto_id', $producto->id)->sum('cantidad');
            if($producto){
                $vendido = DetalleVenta::where('producto_id', $producto->id)->sum('cantidad');
            }else{
                $vendido = 0;
            }
            
            $productosInfo[$producto->id] = [
                'vendido' => $vendido,
                'minimo_permitido' => $vendido,
                'cantidad_original' => $detalle->cantidad
            ];
        }

        $proveedores = Proveedor::with('persona')->get();
        $comprobantes = Comprobante::all();
        $almacenes = \App\Models\Almacen::where('estado', 1)->get();
        
        // Cargamos los productos con el stock total para mostrar en el select
        $productos = Producto::withSum('inventarios as stock', 'stock')
            ->where('estado', 1)
            ->get();

        return view('compra.edit', compact('compra', 'proveedores', 'comprobantes', 'almacenes', 'productos', 'productosInfo'));
    }

    public function update(UpdateCompraRequest $request, Compra $compra)
    {
        try {
            DB::beginTransaction();

            // 1. Validar que se pueda revertir antes de hacer nada
            $this->compraService->validarReversion($compra);

            // 2. Revertir stock actual
            $this->compraService->revertirStock($compra);

            // 3. Borrar detalles viejos
            $compra->detalles()->delete();

            // 4. Actualizar datos básicos
            $compra->update([
                'fecha_hora' => $request->fecha_hora ?? $compra->fecha_hora,
                'numero_comprobante' => $request->numero_comprobante,
                'total' => 0,
                'costo_transporte' => $request->costo_transporte ?? 0,
                'nota_personal' => $request->nota_personal,
                'estado_pago' => $request->estado_pago ?? $compra->estado_pago,
                'estado_entrega' => $request->estado_entrega ?? $compra->estado_entrega,
                'comprobante_id' => $request->comprobante_id,
                'proveedor_id' => $request->proveedor_id,
                'almacen_id' => $request->almacen_id,
            ]);

            // 5. Crear nuevos detalles
            $total = 0;
            foreach ($request->arrayidproducto as $index => $productoId) {
                $cantidad = $request->arraycantidad[$index];
                $precioCompra = $request->arraypreciocompra[$index];
                $precioVenta = $request->arrayprecioventa[$index];

                $compra->detalles()->create([
                    'producto_id' => $productoId,
                    'cantidad' => $cantidad,
                    'precio_compra' => $precioCompra,
                    'precio_venta' => $precioVenta,
                ]);

                $total += ($cantidad * $precioCompra);
            }

            $compra->update(['total' => $total]);

            // 6. Procesar entrada de stock nuevo
            // IMPORTANTE: Recargar la relación detalles porque tiene en caché los viejos (que ya se borraron)
            $compra->refresh(); 
            $this->compraService->procesarEntradaStock($compra);

            DB::commit();
            return redirect()->route('compras.index')->with('success', 'Compra actualizada exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(Compra $compra)
    {
        try {
            DB::beginTransaction();

            // Validar si se puede revertir
            $this->compraService->validarReversion($compra);

            // Revertir stock
            $this->compraService->revertirStock($compra);

            // Borrar detalles y compra
            $compra->detalles()->delete();
            $compra->delete();

            DB::commit();
            return redirect()->route('compras.index')->with('success', 'Compra eliminada y stock revertido.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('compras.index')->with('error', 'No se pudo eliminar: ' . $e->getMessage());
        }
    }
}
