@extends('layouts.app')

@section('title', 'Editar Producto')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
<style>
    /* CONTENEDOR PRINCIPAL COMPACTO - MISMO ESTILO QUE CREATE */
    .form-container {
        background: #ffffff;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border: 1px solid #e6e6e6;
        max-width: 900px;
        margin: auto;
    }

    /* TEXTO CLARO Y LEGIBLE */
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

    /* BOTONES COMPACTOS Y ELEGANTES */
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

    .current-image {
        width: 90px;
        height: 90px;
        object-fit: cover;
        border-radius: 6px;
        border: 2px solid #e6e6e6;
    }

    h1 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1a1a1a;
    }

    .breadcrumb {
        margin-bottom: 1rem;
    }

    /* ESPACIADO COMPACTO PERO LEGIBLE */
    .row.g-3 > div {
        margin-bottom: 0.8rem !important;
    }

    /* ESTILOS ESPECÍFICOS PARA STOCK */
    .stock-control {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 0.8rem;
        border: 1px solid #e9ecef;
    }

    .stock-label {
        font-size: 0.9rem;
        font-weight: 600;
    }

    .stock-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .text-success {
        color: #28a745 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .input-group-text {
        font-size: 0.85rem;
        padding: 0.55rem 0.8rem;
    }

    @media (max-width: 768px) {
        .form-container {
            padding: 1rem;
        }

        .stock-control {
            padding: 0.6rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 px-md-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Editar Producto</h1>
        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
            <li class="breadcrumb-item active">Editar Producto</li>
        </ol>
    </nav>

    <div class="form-container">
        <form action="{{ route('productos.update', ['producto' => $producto]) }}" method="post" enctype="multipart/form-data">
            @method('PATCH')
            @csrf

            <div class="row g-3">
                <!-- Código y Nombre -->
                <div class="col-md-6">
                    <label class="form-label">Código:</label>
                    <input type="text" name="codigo" class="form-control" value="{{ old('codigo', $producto->codigo) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nombre:</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $producto->nombre) }}">
                </div>

                <!-- Descripción -->
                <div class="col-12">
                    <label class="form-label">Descripción:</label>
                    <textarea name="descripcion" rows="2" class="form-control">{{ old('descripcion', $producto->descripcion) }}</textarea>
                </div>

                <!-- Precios -->
                <div class="col-md-6">
                    <label class="form-label">Precio Compra:</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" name="precio_compra" class="form-control"
                               value="{{ old('precio_compra', $producto->precio_compra) }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Precio Venta:</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" name="precio_venta" class="form-control"
                               value="{{ old('precio_venta', $producto->precio_venta) }}">
                    </div>
                </div>

            
                <!-- Control de Stock -->
                <div class="col-12">
                    <div class="stock-control">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="stock-label">Stock Actual:</label>
                                <div class="stock-value">{{ $stock_actual }} unidades</div>
                            </div>
                            <div class="col-md-4">
                                <label class="stock-label text-success">Aumentar:</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="incremento_stock" class="form-control"
                                           min="0" value="0" placeholder="Cantidad">
                                    <span class="input-group-text">+</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="stock-label text-danger">Disminuir:</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="decremento_stock" class="form-control"
                                           min="0" value="0" placeholder="Cantidad">
                                    <span class="input-group-text">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selectores -->
                <div class="col-md-6">
                    <label class="form-label">Marca:</label>
                    <select name="marca_id" class="form-control selectpicker" data-live-search="true">
                        @foreach($marcas as $item)
                            <option value="{{ $item->id }}"
                                {{ $producto->marca_id == $item->id ? 'selected' : '' }}>
                                {{ $item->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Presentación:</label>
                    <select name="presentacione_id" class="form-control selectpicker" data-live-search="true">
                        @foreach($presentaciones as $item)
                            <option value="{{ $item->id }}"
                                {{ $producto->presentacione_id == $item->id ? 'selected' : '' }}>
                                {{ $item->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Categorías:</label>
                    <select name="categorias[]" class="form-control selectpicker" multiple data-live-search="true">
                        @foreach($categorias as $item)
                            <option value="{{ $item->id }}"
                                {{ in_array($item->id, $producto->categorias->pluck('id')->toArray()) ? 'selected' : '' }}>
                                {{ $item->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
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
        preview.innerHTML = '<span class="text-muted">Nueva</span>';
    }
}

// INICIALIZAR BOOTSTRAP SELECT
document.addEventListener('DOMContentLoaded', function() {
    $('.selectpicker').selectpicker();

    // Validar stock
    const incremento = document.querySelector('input[name="incremento_stock"]');
    const decremento = document.querySelector('input[name="decremento_stock"]');

    if (incremento) incremento.addEventListener('change', function() { if (this.value < 0) this.value = 0; });
    if (decremento) decremento.addEventListener('change', function() { if (this.value < 0) this.value = 0; });
});
</script>
@endpush
