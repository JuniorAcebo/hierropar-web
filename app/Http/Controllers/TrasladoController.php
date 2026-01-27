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
use Barryvdh\DomPDF\Facade\Pdf;

class TrasladoController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-traslado', ['only' => ['index']]);
        $this->middleware('permission:crear-traslado', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-traslado', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-traslado', ['only' => ['destroy']]);
        $this->middleware('permission:update-estadoTraslado', ['only' => ['toggleEstado']]);
    }

    public function index(Request $request)
    {
        $busqueda = $request->get('busqueda');
        $perPage  = $request->get('per_page', 10);

        // Validar perPage
        if (!in_array($perPage, [5,10,15,20,25])) {
            $perPage = 10;
        }

        // Construir query
        $query = Traslado::with([
            'origenAlmacen',
            'destinoAlmacen',
            'user',
            'detalles.producto'
        ]);

        if ($busqueda) {
            $query->where(function($q) use ($busqueda) {
                $q->where('id', 'like', "%{$busqueda}%")
                ->orWhereHas('origenAlmacen', function($qa) use ($busqueda) {
                    $qa->where('nombre', 'like', "%{$busqueda}%");
                })
                ->orWhereHas('destinoAlmacen', function($qa) use ($busqueda) {
                    $qa->where('nombre', 'like', "%{$busqueda}%");
                })
                ->orWhereHas('user', function($qa) use ($busqueda) {
                    $qa->where('name', 'like', "%{$busqueda}%");
                });
            });
        }

        // Ordenar y paginar
        $traslados = $query->orderBy('fecha_hora', 'desc')->paginate($perPage);

        // Traer almacenes activos (opcional, para filtros)
        $almacenes = Almacen::where('estado', true)->get();

        return view('traslado.index', compact(
            'traslados',
            'busqueda',
            'perPage',
            'almacenes'
        ));
    }

    public function create()
    {
        $almacenes = Almacen::all();

        $productos = Producto::with('inventarios')
            ->where('estado', 1)
            ->get();

        return view('traslado.create', compact('almacenes', 'productos'));
    }



    public function store(StoreTrasladoRequest $request)
    {
        DB::beginTransaction();

        try {
            $traslado = \App\Models\Traslado::create([
                'origen_almacen_id' => $request->origen_almacen_id,
                'destino_almacen_id' => $request->destino_almacen_id,
                'fecha_hora' => $request->fecha_hora,
                'costo_envio' => $request->costo_envio,
                'user_id' => auth()->id(), // Usuario detectado automáticamente
                'estado' => 1, // Pendiente por defecto
            ]);

            foreach ($request->arrayidproducto as $index => $productoId) {
                $traslado->detalles()->create([
                    'producto_id' => $productoId,
                    'cantidad' => $request->arraycantidad[$index],
                ]);
            }

            DB::commit();
            return redirect()->route('traslados.index')->with('success', 'Traslado creado correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al crear el traslado: '.$e->getMessage()])->withInput();
        }
    }

    public function toggleEstado(Request $request, Traslado $traslado)
    {
        $request->validate([
            'estado' => 'required|in:1,2,3',
        ]);

        DB::beginTransaction();

        try {
            $estadoAnterior = $traslado->estado;
            $estadoNuevo = $request->estado;

            // Solo procesar inventario si cambia a COMPLETADO (estado 2)
            if ($estadoAnterior != 2 && $estadoNuevo == 2) {
                $this->procesarInventarioTraslado($traslado);
            }

            $traslado->estado = $estadoNuevo;
            $traslado->save();

            DB::commit();

            return redirect()->back()->with('success', 'Traslado marcado como completado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }

    /**
     * Procesar el traslado de inventario cuando se marca como completado
     */
    protected function procesarInventarioTraslado(Traslado $traslado)
    {
        $traslado->load('detalles');

        foreach ($traslado->detalles as $detalle) {
            // Restar del almacén origen
            $inventarioOrigen = InventarioAlmacen::where('producto_id', $detalle->producto_id)
                ->where('almacen_id', $traslado->origen_almacen_id)
                ->first();

            if ($inventarioOrigen) {
                $inventarioOrigen->decrement('stock', $detalle->cantidad);
            }

            // Sumar al almacén destino
            $inventarioDestino = InventarioAlmacen::firstOrCreate(
                [
                    'producto_id' => $detalle->producto_id,
                    'almacen_id' => $traslado->destino_almacen_id
                ],
                ['stock' => 0]
            );

            $inventarioDestino->increment('stock', $detalle->cantidad);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Traslado $traslado)
    {
        // Solo permitir editar si está en estado Pendiente (1)
        if ($traslado->estado != 1) {
            return redirect()->route('traslados.index')
                ->with('error', 'Solo puedes editar traslados en estado Pendiente');
        }

        $traslado->load(['detalles.producto.inventarios']);
        $almacenes = Almacen::all();
        $productos = Producto::with('inventarios')
            ->where('estado', 1)
            ->get();

        return view('traslado.edit', compact('traslado', 'almacenes', 'productos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTrasladoRequest $request, Traslado $traslado)
    {
        // Solo permitir editar si está en estado Pendiente (1)
        if ($traslado->estado != 1) {
            return redirect()->route('traslados.index')
                ->with('error', 'Solo puedes editar traslados en estado Pendiente');
        }

        DB::beginTransaction();

        try {
            // Actualizar datos del traslado
            $traslado->update([
                'origen_almacen_id' => $request->origen_almacen_id,
                'destino_almacen_id' => $request->destino_almacen_id,
                'fecha_hora' => $request->fecha_hora,
                'costo_envio' => $request->costo_envio,
            ]);

            // Eliminar detalles anteriores
            $traslado->detalles()->delete();

            // Crear nuevos detalles
            foreach ($request->arrayidproducto as $index => $productoId) {
                $traslado->detalles()->create([
                    'producto_id' => $productoId,
                    'cantidad' => $request->arraycantidad[$index],
                ]);
            }

            DB::commit();
            return redirect()->route('traslados.index')->with('success', 'Traslado actualizado correctamente.');

        } catch (Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al actualizar el traslado: '.$e->getMessage()])->withInput();
        }
    }

    public function destroy(Traslado $traslado)
    {

        DB::beginTransaction();

        try {
            // Eliminar detalles primero (por integridad referencial)
            $traslado->detalles()->delete();

            // Eliminar el traslado
            $traslado->delete();

            DB::commit();
            
            return redirect()->route('traslados.index')->with('success', 'Traslado eliminado correctamente.');

        } catch (Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al eliminar el traslado: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de exportación
     */
    public function exportar()
    {
        return view('traslado.exportar');
    }

    /**
     * Exportar traslados a Excel (CSV)
     */
    public function exportarExcel(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:todos,1,2,3',
        ], [
            'fecha_inicio.required' => 'La fecha de inicio es requerida',
            'fecha_fin.required' => 'La fecha de fin es requerida',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'estado.required' => 'El estado es requerido',
            'estado.in' => 'El estado no es válido',
        ]);

        $fechaInicio = $request->fecha_inicio . ' 00:00:00';
        $fechaFin = $request->fecha_fin . ' 23:59:59';

        $query = Traslado::with(['origenAlmacen', 'destinoAlmacen', 'user', 'detalles.producto'])
            ->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);

        // Filtrar por estado
        if ($request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        $traslados = $query->orderBy('fecha_hora', 'desc')->get();

        if ($traslados->isEmpty()) {
            return back()->with('warning', 'No hay traslados en el rango de fechas especificado');
        }

        // Mapeo de estados
        $estadoMap = [1 => 'Pendiente', 2 => 'Completado', 3 => 'Cancelado'];

        // Crear el archivo CSV
        $filename = 'traslados_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://memory', 'w');

        // Encabezados CSV
        fputcsv($handle, ['ID', 'Fecha', 'Origen', 'Destino', 'Usuario', 'Costo Envío', 'Estado', 'Productos'], ';');

        // Datos
        foreach ($traslados as $traslado) {
            $productos = $traslado->detalles->map(function ($d) {
                return $d->producto->nombre . ' (x' . $d->cantidad . ')';
            })->implode(', ');

            fputcsv($handle, [
                $traslado->id,
                $traslado->fecha_hora->format('d/m/Y H:i'),
                $traslado->origenAlmacen->nombre,
                $traslado->destinoAlmacen->nombre,
                $traslado->user->name,
                number_format($traslado->costo_envio, 2, ',', '.'),
                $estadoMap[$traslado->estado],
                $productos
            ], ';');
        }

        // Retroceder al principio del archivo
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        // Agregar BOM para que Excel reconozca la codificación UTF-8
        $csv = "\xEF\xBB\xBF" . $csv;

        // Enviar el archivo
        return response($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Exportar traslados a PDF
     */
    public function exportarPdf(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:todos,1,2,3',
        ], [
            'fecha_inicio.required' => 'La fecha de inicio es requerida',
            'fecha_fin.required' => 'La fecha de fin es requerida',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
            'estado.required' => 'El estado es requerido',
            'estado.in' => 'El estado no es válido',
        ]);

        $fechaInicio = $request->fecha_inicio . ' 00:00:00';
        $fechaFin = $request->fecha_fin . ' 23:59:59';

        $query = Traslado::with(['origenAlmacen', 'destinoAlmacen', 'user', 'detalles.producto'])
            ->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);

        // Filtrar por estado
        if ($request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        $traslados = $query->orderBy('fecha_hora', 'desc')->get();

        if ($traslados->isEmpty()) {
            return back()->with('warning', 'No hay traslados en el rango de fechas especificado');
        }

        $pdf = Pdf::loadView('traslado.pdf', compact('traslados'));
        return $pdf->download('traslados_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

}
