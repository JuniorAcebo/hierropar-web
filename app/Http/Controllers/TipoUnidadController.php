<?php

namespace App\Http\Controllers;
use App\Models\TipoUnidad;
use App\Http\Requests\StoreTipoUnidadRequest;
use App\Http\Requests\UpdateTipoUnidadRequest;

class TipoUnidadController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-tipounidad', ['only' => ['index']]);
        $this->middleware('permission:crear-tipounidad', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-tipounidad', ['only' => ['edit', 'update']]);
    }

    public function index()
    {
        $tipounidades = TipoUnidad::orderBy('nombre')->get();
        return view('tipounidad.index', compact('tipounidades'));
    }

    public function create()
    {
        return view('tipounidad.create');
    }

    public function store(StoreTipoUnidadRequest $request)
    {
        TipoUnidad::create($request->validated());

        return redirect()
            ->route('tipounidades.index')
            ->with('success', 'Tipo de Unidad registrada correctamente');
    }

    public function edit(TipoUnidad $tipounidad)
    {
        return view('tipounidad.edit', compact('tipounidad'));
    }

    public function update(UpdateTipoUnidadRequest $request, TipoUnidad $tipounidad)
    {
        $tipounidad->update($request->validated());

        return redirect()
            ->route('tipounidades.index')
            ->with('success', 'Tipo de Unidad actualizada correctamente');
    }
}