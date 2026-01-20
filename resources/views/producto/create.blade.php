@extends('layouts.app')

@section('title', 'Crear Producto')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="{{ asset('css/style_create_edit_Producto.css') }}">
@endpush

@section('content')
<div class="container-fluid px-2 px-md-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Crear Producto</h1>
        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
            <li class="breadcrumb-item active">Nuevo Producto</li>
        </ol>
    </nav>

    <div class="form-container">
        <form action="{{ route('productos.store') }}" method="post">
            @csrf
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Código:</label>
                    <div class="d-flex">
                        <input type="text" name="codigo" id="codigo" class="form-control" value="{{ old('codigo') }}">
                        <button type="button" onclick="generarCodigo()" class="generar-btn">Generar</button>
                    </div>
                    @error('codigo') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nombre:</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}">
                    @error('nombre') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Precio Compra:</label>
                    <div class="input-group">
                        <span class="input-group-text">Bs</span>
                        <input type="number" step="0.01" name="precio_compra" class="form-control" value="{{ old('precio_compra', 0.00) }}">
                    </div>
                    @error('precio_compra') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Precio Venta:</label>
                    <div class="input-group">
                        <span class="input-group-text">Bs</span>
                        <input type="number" step="0.01" name="precio_venta" class="form-control" value="{{ old('precio_venta', 0.00) }}">
                    </div>
                    @error('precio_venta') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Descripción:</label>
                    <textarea name="descripcion" rows="2" class="form-control">{{ old('descripcion') }}</textarea>
                    @error('descripcion') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <!-- STOCK POR TODOS LOS ALMACENES (SIEMPRE) -->
                <div class="col-12">
                    <label class="form-label">Stock por almacén:</label>

                    @foreach($almacenes->where('estado', true) as $almacen) {{-- Solo activos --}}
                        <div class="input-group mb-2">
                            <span class="input-group-text" style="min-width: 180px;">
                                {{ $almacen->nombre }}
                            </span>
                            <input type="number"
                                class="form-control"
                                name="stock_todos[{{ $almacen->id }}]"
                                min="0"
                                value="0">
                        </div>
                    @endforeach
                </div>

              

                <div class="col-md-6">
                    <label class="form-label">Marca:</label>
                    <select name="marca_id" class="form-control selectpicker" >
                        @foreach($marcas as $marca)
                            <option value="{{ $marca->id }}">{{ $marca->nombre }}</option>
                        @endforeach
                    </select>
                    @error('marca_id') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Tipo Unidad:</label>
                    <select name="tipounidad_id" class="form-control selectpicker" >
                        @foreach($tipounidades as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                    @error('tipounidad_id') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Categoría:</label>
                    <select name="categoria_id" class="form-control selectpicker" >
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                    @error('categoria_id') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<script>
function generarCodigo() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = 'PROD-';
    for (let i = 0; i < 5; i++) code += chars.charAt(Math.floor(Math.random()*chars.length));
    document.getElementById('codigo').value = code;
}

document.addEventListener('DOMContentLoaded', function() {
    $('.selectpicker').selectpicker();
    if (!document.getElementById('codigo').value) generarCodigo();
});
</script>
@endpush
