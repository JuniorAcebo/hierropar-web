<?php

namespace App\Http\Controllers;

use App\Models\CorteTablero;
use App\Models\Cliente;
use Illuminate\Http\Request;

class CorteTableroController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ver-corte-tablero|crear-corte-tablero|editar-corte-tablero|eliminar-corte-tablero|mostrar-corte-tablero', ['only' => ['index']]);
        $this->middleware('permission:crear-corte-tablero', ['only' => ['create', 'store']]);
        $this->middleware('permission:editar-corte-tablero', ['only' => ['edit', 'update']]);
        $this->middleware('permission:eliminar-corte-tablero', ['only' => ['destroy']]);
        $this->middleware('permission:mostrar-corte-tablero', ['only' => ['show']]);
    }

    public function index()
    {
        $cortes = CorteTablero::with('cliente.persona')
            ->latest()
            ->paginate(10);

        return view('corte-tablero.index', compact('cortes'));
    }

    public function create()
    {
        $clientes = Cliente::with('persona')
            ->whereHas('persona', fn($q) => $q->where('estado', 1))
            ->get();

        return view('corte-tablero.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'nombre_trabajo' => 'required|string|max:255',
            'largo_tablero' => 'required|numeric|min:0.1',
            'ancho_tablero' => 'required|numeric|min:0.1',
            'cantidad_tableros' => 'required|integer|min:1',
            'piezas' => 'required|array|min:1',
            'piezas.*.largo' => 'required|numeric|min:0.1',
            'piezas.*.ancho' => 'required|numeric|min:0.1',
            'piezas.*.cantidad' => 'required|integer|min:1',
            'piezas.*.descripcion' => 'nullable|string|max:255'
        ]);

        $corte = CorteTablero::create([
            'cliente_id' => $request->cliente_id,
            'nombre_trabajo' => $request->nombre_trabajo,
            'descripcion' => $request->descripcion,
            'largo_tablero' => $request->largo_tablero,
            'ancho_tablero' => $request->ancho_tablero,
            'cantidad_tableros' => $request->cantidad_tableros,
            'piezas' => $request->piezas
        ]);

        // Calcular totales
        $corte->calcularTotales();
        $corte->save();

        return redirect()->route('cortes-tablero.index')
            ->with('success', 'Corte de tablero creado exitosamente');
    }

    public function show(CorteTablero $cortesTablero)
    {
        return view('corte-tablero.show', compact('cortesTablero'));
    }

    public function edit(CorteTablero $cortesTablero)
    {
        $clientes = Cliente::with('persona')
            ->whereHas('persona', fn($q) => $q->where('estado', 1))
            ->get();

        return view('corte-tablero.edit', compact('cortesTablero', 'clientes'));
    }

    public function update(Request $request, CorteTablero $cortesTablero)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'nombre_trabajo' => 'required|string|max:255',
            'largo_tablero' => 'required|numeric|min:0.1',
            'ancho_tablero' => 'required|numeric|min:0.1',
            'cantidad_tableros' => 'required|integer|min:1',
            'piezas' => 'required|array|min:1',
            'piezas.*.largo' => 'required|numeric|min:0.1',
            'piezas.*.ancho' => 'required|numeric|min:0.1',
            'piezas.*.cantidad' => 'required|integer|min:1',
            'piezas.*.descripcion' => 'nullable|string|max:255'
        ]);

        $cortesTablero->update([
            'cliente_id' => $request->cliente_id,
            'nombre_trabajo' => $request->nombre_trabajo,
            'descripcion' => $request->descripcion,
            'largo_tablero' => $request->largo_tablero,
            'ancho_tablero' => $request->ancho_tablero,
            'cantidad_tableros' => $request->cantidad_tableros,
            'piezas' => $request->piezas
        ]);

        // Recalcular totales
        $cortesTablero->calcularTotales();
        $cortesTablero->save();

        return redirect()->route('cortes-tablero.index')
            ->with('success', 'Corte de tablero actualizado exitosamente');
    }

    public function destroy(CorteTablero $cortesTablero)
    {
        $cortesTablero->delete();

        return redirect()->route('cortes-tablero.index')
            ->with('success', 'Corte de tablero eliminado exitosamente');
    }
}
