<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlmacenRequest;
use App\Http\Requests\UpdateAlmacenRequest;
use App\Models\Almacen;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-almacen', ['only' => ['index']]);
        $this->middleware('permission:crear-almacen', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-almacen', ['only' => ['edit', 'update']]);
        $this->middleware('permission:dar-de-baja-almacen', ['only' => ['destroy']]);
    }

    public function index()
    {
        $almacenes = Almacen::orderBy('nombre')->get();
        return view('almacen.index', compact('almacenes'));
    }

    public function create()
    {
        return view('almacen.create');
    }

    public function store(StoreAlmacenRequest $request)
    {

        Almacen::create($request->validated());

        return redirect()
            ->route('almacenes.index')
            ->with('success', 'Almacén registrado correctamente');
    }

    public function edit(Almacen $almacen)
    {
        return view('almacen.edit', compact('almacen'));
    }


   
    public function update(UpdateAlmacenRequest $request, Almacen $almacen)
    {

        $almacen->update($request->validated());

        return redirect()
            ->route('almacenes.index')
            ->with('success', 'Almacén actualizado correctamente');
    }

    public function updateEstado(Almacen $almacen)
    {
        $almacen->estado = ! $almacen->estado;
        $almacen->save();

        $mensaje = $almacen->estado
            ? 'Almacén activado correctamente'
            : 'Almacén desactivado correctamente';

        return redirect()->route('almacenes.index')->with('success', $mensaje);
    }


}