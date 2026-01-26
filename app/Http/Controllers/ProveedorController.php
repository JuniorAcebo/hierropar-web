<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdateProveedoreRequest;
use App\Models\Documento;
use App\Models\Persona;
use App\Models\Proveedor;
use Exception;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-proveedor|crear-proveedor|editar-proveedor|dar-de-baja-proveedor', ['only' => ['index']]);
        $this->middleware('permission:crear-proveedor', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-proveedor', ['only' => ['edit', 'update']]);
        $this->middleware('permission:dar-de-baja-proveedor', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $proveedores = Proveedor::with('persona.documento')->get();
            return view('proveedor.index',compact('proveedores'));
        }catch(Exception $e){
            return redirect()->route('proveedores.index')->with('error', 'Error al mostrar proveedores');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try{
            $documentos = Documento::all();
            return view('proveedor.create',compact('documentos'));
        }catch(Exception $e){
            return redirect()->route('proveedores.create')->with('error', 'Error al mostrar proveedores');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonaRequest $request)
    {
        try{
            DB::beginTransaction();
            $persona = Persona::create($request->validated());
            $persona->proveedor()->create([
                'persona_id' => $persona->id
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $proveedor = Proveedor::with('persona.documento')->find($id);
            return view('proveedor.show',compact('proveedor'));
        }catch(Exception $e){   
            return redirect()->route('proveedores.show')->with('error', 'Error al mostrar proveedor');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proveedore $proveedore)
    {
        try{
            $proveedore->load('persona.documento');
            $documentos = Documento::all();
            return view('proveedor.edit',compact('proveedore','documentos'));
        }catch(Exception $e){
            return redirect()->route('proveedores.edit')->with('error', 'Error al mostrar proveedor');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProveedoreRequest $request, Proveedore $proveedore)
    {
        try{
            DB::beginTransaction();

            Persona::where('id',$proveedore->persona->id)
            ->update($request->validated());

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
        }

        return redirect()->route('proveedores.index')->with('success','Proveedor editado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = '';
        $persona = Persona::find($id);
        if ($persona->estado == 1) {
            Persona::where('id', $persona->id)
                ->update([
                    'estado' => 0
                ]);
            $message = 'Proveedor eliminado';
        } else {
            Persona::where('id', $persona->id)
                ->update([
                    'estado' => 1
                ]);
            $message = 'Proveedor restaurado';
        }

        return redirect()->route('proveedores.index')->with('success', $message);
    }
}
