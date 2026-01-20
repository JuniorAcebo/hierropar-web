<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCaracteristicaRequest;
use App\Http\Requests\UpdateMarcaRequest;
use App\Models\Marca;
use Illuminate\Support\Facades\DB;

class MarcaController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-marca|crear-marca|editar-marca|eliminar-marca', ['only' => ['index']]);
        $this->middleware('permission:crear-marca', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-marca', ['only' => ['edit', 'update']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $marcas = Marca::orderBy('nombre')->get();
        return view('marca.index', compact('marcas'));
    }

    public function create()
    {
        return view('marca.create');
    }

    public function store(StoreCaracteristicaRequest $request)
    {
        Marca::create($request->validated());

        return redirect()
            ->route('marcas.index')
            ->with('success', 'Marca registrada correctamente');
    }
    
    public function edit(Marca $marca)
    {
        return view('marca.edit',compact('marca'));
    }

    
    public function update(UpdateMarcaRequest $request, Marca $marca)
    {
        $marca->update($request->validated());

        return redirect()
            ->route('marcas.index')
            ->with('success', 'Marca actualizada correctamente');
    }

}
