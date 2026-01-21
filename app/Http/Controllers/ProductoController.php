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
        $this->middleware('permission:update-estado', ['only' => ['updateEstado']]);
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

        return view('producto.index', compact(
            'productos',
            'busqueda',
            'perPage'
        ));
    }

    
    public function create()
    {
        $marcas = Marca::all();
        $tipounidades = TipoUnidad::all();
        $categorias = Categoria::all();

        return view('producto.create', compact('marcas', 'tipounidades', 'categorias'));
    }

    public function store(StoreProductoRequest $request)
    {
        try {
            DB::beginTransaction();

            // 1️⃣ Crear producto
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

            // 2️⃣ Obtener TODOS los almacenes activos
            $almacenes = Almacen::where('estado', true)->get();

            // 3️⃣ Crear inventario con stock = 0
            foreach ($almacenes as $almacen) {
                $producto->inventarios()->create([
                    'almacen_id' => $almacen->id,
                    'stock' => 0,
                ]);
            }

            DB::commit();

            return redirect()->route('productos.index')
                ->with('success', 'Producto creado y agregado a todos los almacenes.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Error al crear producto: ' . $e->getMessage());
        }
    }

    public function edit(Producto $producto)
    {
        $marcas = Marca::all();
        $tipounidades = TipoUnidad::all();
        $categorias = Categoria::all();

        return view('producto.edit', compact(
            'producto',
            'marcas',
            'tipounidades',
            'categorias'
        ));
    }



    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        try {
            $producto->update([
                'nombre'         => $request->nombre,
                'descripcion'    => $request->descripcion,
                'precio_compra'  => $request->precio_compra,
                'precio_venta'   => $request->precio_venta,
                'marca_id'       => $request->marca_id,
                'tipounidad_id'  => $request->tipounidad_id,
                'categoria_id'   => $request->categoria_id,
            ]);

            return redirect()->route('productos.index')
                ->with('success', 'Producto actualizado correctamente.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    
    public function updateEstado(Producto $producto)
    {
        $producto->estado = !$producto->estado;
        $producto->save();

        return redirect()->route('productos.index')
            ->with('success', $producto->estado
                ? 'Producto activado correctamente.'
                : 'Producto desactivado correctamente.');
    }



}
