@extends('layouts.app')

@section('title', 'Ajuste de Stock')

@push('css')
<style>
    .card-adjustment {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        background: #ffffff;
    }
    .header-adjustment {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 20px;
    }
    .form-label-custom {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
    }
    .btn-adjustment {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-adjustment:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }
    .input-custom {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 12px;
    }
    .input-custom:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
</style>
<script>
    document.getElementById('tipo_ajuste').addEventListener('change', function() {
        const helpText = document.getElementById('helpText');
        if (this.value === 'sumar') {
            helpText.innerText = 'Se sumará la cantidad indicada al stock actual.';
        } else if (this.value === 'restar') {
            helpText.innerText = 'Se restará la cantidad indicada al stock actual.';
        } else {
            helpText.innerText = 'El stock total pasará a ser exactamente la cantidad indicada.';
        }
    });
</script>
@endpush

@section('content')
@include('layouts.partials.alert')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card card-adjustment">
                <div class="header-adjustment">
                    <h3 class="mb-0"><i class="fas fa-boxes me-2"></i>Ajuste de Cantidad</h3>
                    <p class="mb-0 mt-2 opacity-75">Producto: {{ $producto->nombre }}</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                @endif

                <div class="card-body p-4">

                    <form action="{{ route('productos.updateCantidad', $producto) }}" method="POST">
                        @csrf
                        @method('POST')

                        <div class="mb-4">
                            <label for="almacen_id" class="form-label-custom">Seleccionar Sucursal / Almacén</label>
                            <select name="almacen_id" id="almacen_id" class="form-select input-custom" required>
                                <option value="" disabled selected>Seleccione un almacén...</option>
                                @foreach($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}">
                                        {{ $almacen->nombre }} (Stock actual: {{ $producto->inventarios->where('almacen_id', $almacen->id)->first()->stock ?? 0 }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="tipo_ajuste" class="form-label-custom">Operación</label>
                            <select name="tipo_ajuste" id="tipo_ajuste" class="form-select input-custom" required>
                                <option value="sumar">➕ Aumentar Stock (+)</option>
                                <option value="restar">➖ Restar Stock (-)</option>
                                <option value="fijar">🎯 Fijar Cantidad Total (Manual)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="cantidad" class="form-label-custom">Cantidad</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="fas fa-sort-numeric-up"></i></span>
                                <input type="number" step="0.01" name="cantidad" id="cantidad" class="form-control input-custom border-start-0" placeholder="Ej: 10.50" required min="0">
                            </div>
                            <small class="text-muted mt-2 d-block" id="helpText">Suma o resta la cantidad indicada al stock actual.</small>
                        </div>

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" class="btn btn-adjustment">
                                <i class="fas fa-save me-2"></i>Guardar Ajuste
                            </button>
                            <a href="{{ route('productos.index') }}" class="btn btn-light border py-2" style="border-radius: 8px; font-weight: 600;">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
