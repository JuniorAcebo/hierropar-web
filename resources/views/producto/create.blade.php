@extends('layouts.app')

@section('title', 'Crear Producto')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    .form-container {
        background: #ffffff;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e6e6e6;
        max-width: 900px;
        margin: auto;
    }

    .form-label {
        font-weight: 600;
        color: #1a1a1a;
        font-size: 1.05rem;
        margin-bottom: 0.3rem;
    }

    .form-control, .selectpicker {
        border: 1.8px solid #cfcfcf;
        border-radius: 6px;
        padding: 0.55rem 0.8rem;
        font-size: 0.95rem;
    }

    .form-control:focus, .bootstrap-select .btn:focus {
        border-color: #4c6ef5;
        box-shadow: 0 0 0 2px rgba(76, 110, 245, 0.25);
    }

    .btn-primary {
        background: #4c6ef5;
        border: none;
        padding: 0.7rem 2rem;
        border-radius: 6px;
        font-size: 1.05rem;
        font-weight: 600;
    }

    .btn-primary:hover {
        background: #3a57d3;
    }

    .btn-outline-secondary {
        padding: 0.55rem 1.2rem;
        border-radius: 6px;
        font-size: 0.95rem;
    }

    .generar-btn {
        background: #2d3436;
        color: white;
        border: none;
        padding: 0.55rem 0.9rem;
        border-radius: 6px;
        font-size: 0.85rem;
        margin-left: 0.4rem;
    }

    /* VISTA PREVIA DE IMAGEN PEQUEÑA Y CLARA */
    .image-preview {
        width: 90px;
        height: 90px;
        border: 1.5px dashed #cccccc;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        background: #fafafa;
    }

    h1 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1a1a1a;
    }

    .breadcrumb {
        margin-bottom: 1rem;
    }

    /* Espaciado compacto pero legible */
    .row.g-3 > div {
        margin-bottom: 0.8rem !important;
    }

    @media (max-width: 768px) {
        .form-container {
            padding: 1rem;
        }
    }
</style>
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
        <form action="{{ route('productos.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Código:</label>
                    <div class="d-flex">
                        <input type="text" name="codigo" id="codigo" class="form-control" value="{{ old('codigo') }}">
                        <button type="button" onclick="generarCodigo()" class="generar-btn">Generar</button>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nombre:</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Precio Compra:</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" name="precio_compra" class="form-control" value="0.00">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Precio Venta:</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" name="precio_venta" class="form-control" value="0.00">
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Descripción:</label>
                    <textarea name="descripcion" rows="2" class="form-control">{{ old('descripcion') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Fecha de Vencimiento:</label>
                    <input type="date" name="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Imagen:</label>
                    <input type="file" name="img_path" class="form-control" onchange="previewImage(this)">
                    <div class="image-preview mt-1" id="imagePreview">Vista previa</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Marca:</label>
                    <select name="marca_id" class="form-control selectpicker" data-live-search="true">
                        @foreach($marcas as $item)
                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Presentación:</label>
                    <select name="presentacione_id" class="form-control selectpicker" data-live-search="true">
                        @foreach($presentaciones as $item)
                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Categorías:</label>
                    <select name="categorias[]" class="form-control selectpicker" multiple data-live-search="true">
                        @foreach($categorias as $item)
                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                        @endforeach
                    </select>
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
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => preview.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; max-height: 100%;">`;
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = 'Vista previa';
    }
}

function generarCodigo() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = 'PROD-';
    for (let i = 0; i < 5; i++) code += chars.charAt(Math.floor(Math.random()*chars.length));
    document.getElementById('codigo').value = code;
}

// INICIALIZAR BOOTSTRAP SELECT
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar todos los select con clase selectpicker
    $('.selectpicker').selectpicker();

    // Generar código automáticamente si está vacío
    if (!document.getElementById('codigo').value) {
        generarCodigo();
    }
});
</script>
@endpush
