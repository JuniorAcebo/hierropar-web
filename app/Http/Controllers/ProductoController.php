<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Almacen;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\TipoUnidad;
use App\Models\Producto;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-producto', ['only' => ['index']]);
        $this->middleware('permission:crear-producto', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-producto', ['only' => ['edit', 'update']]);
        $this->middleware('permission:update-stock', ['only' => ['updateStock','addAlmacen']]);
        $this->middleware('permission:update-estado', ['only' => ['updateState']]);
    }
    
    public function index(Request $request)
    {
        $busqueda = $request->get('busqueda');
        $perPage  = $request->get('per_page', 10);

        if (!in_array($perPage, [5,10,15,20,25])) {
            $perPage = 10;
        }

        $query = Producto::with([
            'marca',
            'categoria',
            'tipounidad',
            'inventarios.almacen'
        ]);

        if ($busqueda) {
            $query->where(function ($q) use ($busqueda) {
                $q->where('codigo', 'like', "%{$busqueda}%")
                ->orWhere('nombre', 'like', "%{$busqueda}%")
                ->orWhere('descripcion', 'like', "%{$busqueda}%");
            });
        }

        $productos = $query->latest()->paginate($perPage);

         $almacenes = Almacen::all();

        return view('producto.index', compact(
            'productos',
            'busqueda',
            'perPage',
            'almacenes'
        ));
    }

    
    public function create()
    {
        $marcas = Marca::all();
        $tipounidades = TipoUnidad::all();
        $categorias = Categoria::all();
        $almacenes = Almacen::all();

        return view('producto.create', compact('marcas', 'tipounidades', 'categorias', 'almacenes'));
    }

    public function store(StoreProductoRequest $request)
    {
        try {
            DB::beginTransaction();

            // Crear producto
            $producto = Producto::create([
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'precio_compra' => $request->precio_compra,
                'precio_venta' => $request->precio_venta,
                'marca_id' => $request->marca_id,
                'tipounidad_id' => $request->tipounidad_id,
                'categoria_id' => $request->categoria_id,
                'estado' => true,
            ]);

            // =============================
            // REGISTRO DE STOCK EN TODOS LOS ALMACENES
            // =============================
            if ($request->has('stock_todos') && is_array($request->stock_todos)) {
                foreach ($request->stock_todos as $almacen_id => $stock) {
                    $producto->inventarios()->create([
                        'almacen_id' => $almacen_id,
                        'stock'      => floatval($stock) ?? 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('productos.index')
                ->with('success', 'Producto creado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }


    public function edit(Producto $producto)
    {

    }


    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        
    }
    
    public function destroy(string $id)
    {
        
    }

    public function updateStock(Request $request, Producto $producto)
    {
        $stocks = $request->input('stocks', []);

        foreach ($stocks as $almacen_id => $cantidad) {
            $cantidad = floatval($cantidad);
            if ($cantidad <= 0) continue;

            $inv = $producto->inventarios()->where('almacen_id', $almacen_id)->first();
            if ($inv) {
                $inv->increment('stock', $cantidad);
            }
        }

        return redirect()->route('productos.index')
            ->with('success', 'Stock actualizado correctamente');
    }




    public function addAlmacen(Request $request, Producto $producto)
    {
        $producto->inventarios()->create([
            'almacen_id' => $request->almacen_id,
            'stock' => $request->stock ?? 0,
        ]);

        return redirect()->route('productos.index')
            ->with('success', 'Producto agregado al almacén');
    }


}
