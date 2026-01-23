<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrasladoController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:ver-traslado', ['only' => ['index']]);
        $this->middleware('permission:crear-traslado', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-traslado', ['only' => ['edit', 'update']]); 
        $this->middleware('permission:update-estado', ['only' => ['updateEstado']]);
    }
    
    public function index(Request $request)
    {
        
    }

    
    public function create()
    {
        
    }

    public function store(StoreProductoRequest $request)
    {
        
    }

    public function edit(Producto $producto)
    {
        
    }



    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        
    }

    
    public function updateEstado(Producto $producto)
    {
        
    }
}
