<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Presentacione;
use App\Models\Producto;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-producto|crear-producto|editar-producto|eliminar-producto', ['only' => ['index']]);
        $this->middleware('permission:crear-producto', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-producto', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-producto', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    // En ProductoController.aphp
    public function index(\Illuminate\Http\Request $request)
    {
        $busqueda = $request->get('busqueda');
        $perPage = $request->get('per_page', 10);

        // Validar que per_page sea un valor permitido
        if (!in_array($perPage, [5, 10, 15, 20, 25])) {
            $perPage = 10;
        }

        $query = Producto::with(['categorias.caracteristica', 'marca.caracteristica', 'presentacione.caracteristica'])
            ->with(['compras' => function ($query) {
                $query->latest()->limit(1); // Solo la última compra
            }]);

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('codigo', 'LIKE', "%{$busqueda}%")
                    ->orWhere('nombre', 'LIKE', "%{$busqueda}%")
                    ->orWhere('descripcion', 'LIKE', "%{$busqueda}%");
            });
        }

        $productos = $query->latest()->paginate($perPage);

        $productos = $query->latest()->paginate($perPage);

        return view('producto.index', compact('productos', 'busqueda', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $marcas = Marca::join('caracteristicas as c', 'marcas.caracteristica_id', '=', 'c.id')
            ->select('marcas.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        $presentaciones = Presentacione::join('caracteristicas as c', 'presentaciones.caracteristica_id', '=', 'c.id')
            ->select('presentaciones.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        $categorias = Categoria::join('caracteristicas as c', 'categorias.caracteristica_id', '=', 'c.id')
            ->select('categorias.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        return view('producto.create', compact('marcas', 'presentaciones', 'categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductoRequest $request)
    {
        try {
            DB::beginTransaction();

            $producto = new Producto();
            $name = null;

            if ($request->hasFile('img_path')) {
                try {
                    $name = $producto->handleUploadImage($request->file('img_path'));
                } catch (\Exception $e) {
                    return redirect()->back()->withInput()->with('error', 'Error al subir imagen: ' . $e->getMessage());
                }
            }

            $producto->fill([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'img_path' => $name,
                'marca_id' => $request->marca_id,
                'presentacione_id' => $request->presentacione_id,
                'precio_compra' => $request->precio_compra ?? 0,
                'precio_venta' => $request->precio_venta ?? 0,
                'stock' => 0 // Valor por defecto
            ]);

            $producto->save();
            $producto->categorias()->attach($request->get('categorias', []));

            DB::commit();
            return redirect()->route('productos.index')->with('success', 'Producto registrado');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al registrar: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $marcas = Marca::join('caracteristicas as c', 'marcas.caracteristica_id', '=', 'c.id')
            ->select('marcas.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        $presentaciones = Presentacione::join('caracteristicas as c', 'presentaciones.caracteristica_id', '=', 'c.id')
            ->select('presentaciones.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        $categorias = Categoria::join('caracteristicas as c', 'categorias.caracteristica_id', '=', 'c.id')
            ->select('categorias.id as id', 'c.nombre as nombre')
            ->where('c.estado', 1)
            ->get();

        // Obtener el stock actual
        $stock_actual = $producto->stock;

        return view('producto.edit', compact('producto', 'marcas', 'presentaciones', 'categorias', 'stock_actual'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        try {
            DB::beginTransaction();

            // Bloquear registro para evitar race conditions
            $productoBloqueado = Producto::lockForUpdate()->find($producto->id);

            // Manejo de imagen
            if ($request->hasFile('img_path')) {
                $name = $productoBloqueado->handleUploadImage($request->file('img_path'));

                // Borrar imagen anterior solo si existe
                if ($productoBloqueado->img_path && Storage::disk('public')->exists('productos/' . $productoBloqueado->img_path)) {
                    Storage::disk('public')->delete('productos/' . $productoBloqueado->img_path);
                }
            } else {
                $name = $productoBloqueado->img_path;
            }

            // Calcular nuevo stock considerando incremento y decremento
            $nuevo_stock = $productoBloqueado->stock;

            if ($request->filled('incremento_stock') && $request->incremento_stock > 0) {
                $nuevo_stock += $request->incremento_stock;
            }

            if ($request->filled('decremento_stock') && $request->decremento_stock > 0) {
                // Validar que no quede stock negativo
                if ($request->decremento_stock > $nuevo_stock) {
                    throw new Exception('No se puede disminuir más stock del disponible');
                }
                $nuevo_stock -= $request->decremento_stock;
            }

            // Actualización
            $productoBloqueado->fill([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'img_path' => $name,
                'marca_id' => $request->marca_id,
                'presentacione_id' => $request->presentacione_id,
                'precio_compra' => $request->precio_compra,
                'precio_venta' => $request->precio_venta,
                'stock' => $nuevo_stock
            ]);

            $productoBloqueado->save();
            $productoBloqueado->categorias()->sync($request->get('categorias', []));

            DB::commit();

            // Mensaje personalizado
            $mensaje = 'Producto actualizado';
            if ($request->filled('incremento_stock') && $request->incremento_stock > 0) {
                $mensaje .= '. Stock aumentado en ' . $request->incremento_stock . ' unidades';
            }
            if ($request->filled('decremento_stock') && $request->decremento_stock > 0) {
                $mensaje .= '. Stock disminuido en ' . $request->decremento_stock . ' unidades';
            }

            return redirect()->route('productos.index')->with('success', $mensaje);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $producto = Producto::findOrFail($id);

            // Verificar si tiene movimientos
            if ($producto->compras()->exists() || $producto->ventas()->exists()) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar, el producto tiene movimientos registrados');
            }

            // ELIMINACIÓN FÍSICA (si no tiene movimientos)
            $producto->delete(); // ← Esto borraría físicamente el registro

            return redirect()->route('productos.index')->with('success', 'Producto eliminado permanentemente');
        } catch (Exception $e) {
            return redirect()->route('productos.index')
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}
