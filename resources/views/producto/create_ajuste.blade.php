@extends('layouts.app')

@section('title', 'Nuevo Ajuste de Stock')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 45px;
        display: flex;
        align-items: center;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .card-custom {
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    .btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header bg-dark text-white p-3" style="border-radius: 15px 15px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Registrar Nuevo Ajuste de Stock</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('productos.storeAjuste') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold">1. Seleccionar Producto</label>
                            <select name="producto_id" id="producto_id" class="form-select select2" required>
                                <option value="">Busque un producto por nombre o código...</option>
                                @foreach($productos as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->codigo }} - {{ $prod->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-bold">2. Sucursal / Almacén</label>
                                <select name="almacen_id" class="form-select" required>
                                    <option value="">Seleccione...</option>
                                    @foreach($almacenes as $alm)
                                        <option value="{{ $alm->id }}">{{ $alm->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-bold">3. Operación</label>
                                <select name="tipo_ajuste" id="tipo_ajuste" class="form-select" required>
                                    <option value="sumar">➕ Aumentar (+)</option>
                                    <option value="restar">➖ Restar (-)</option>
                                    <option value="fijar">🎯 Fijar Total</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-bold">4. Cantidad</label>
                                <input type="number" step="0.01" name="cantidad" class="form-control" placeholder="0.00" required min="0">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Motivo del Ajuste (Opcional)</label>
                            <textarea name="motivo" class="form-control" rows="2" placeholder="Ej: Corrección de inventario por rotura"></textarea>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-save">
                                <i class="fas fa-save me-2"></i>Guardar Ajuste de Stock
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: "classic",
            width: '100%'
        });
    });
</script>
@endpush
