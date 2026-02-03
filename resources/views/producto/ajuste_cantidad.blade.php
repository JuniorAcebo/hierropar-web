@extends('layouts.app')

@section('title', 'Ajuste de Cantidad')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 0.9rem;
        }

        .card-clean {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #fff;
        }

        .card-header-clean {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .card-header-title {
            font-weight: 600;
            font-size: 1rem;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #adb5bd;
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.15);
        }

        .select2-container .select2-selection--single {
            height: 38px;
            display: flex;
            align-items: center;
            border-radius: 6px;
            border: 1px solid #ced4da;
            font-size: 0.9rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 0.75rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }

        .btn-primary-clean {
            background: #495057;
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.15s;
        }

        .btn-primary-clean:hover {
            background: #343a40;
            color: white;
        }

        .section-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: #e9ecef;
            color: #495057;
            border-radius: 50%;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 8px;
        }

        .form-section {
            margin-bottom: 1.5rem;
        }

        /* Small adjustments for the producto page */
        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #343a40;
            margin-top: 0.25rem;
        }
    </style>
@endpush

@section('content')
    @include('layouts.partials.alert')

    <div class="container-fluid px-4 py-4">

        <div class="page-header">
            <div>
                <h1 class="page-title">Ajuste de Cantidad</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('productos.index') }}" class="text-decoration-none text-muted">Productos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ajuste Cantidad</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card-clean">
                    <div class="card-header-clean">
                        <div class="card-header-title">
                            <i class="fas fa-boxes"></i> <div class="product-title">{{ $producto->codigo ?? '' }} - {{ $producto->nombre }}</div>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('productos.updateCantidad', $producto) }}" method="POST">
                            @csrf

                            <div class="form-section">
                                <label class="form-label">Sucursal / Almacén</label>
                                <select name="almacen_id" id="almacen_id" class="form-select" required>
                                    <option value="">Seleccione un almacén...</option>
                                    @foreach($almacenes as $alm)
                                        <option value="{{ $alm->id }}" {{ old('almacen_id') == $alm->id ? 'selected' : '' }}>
                                            {{ $alm->nombre }} (Stock actual: {{ $producto->inventarios->where('almacen_id', $alm->id)->first()->stock ?? 0 }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('almacen_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4 form-section">
                                    <label class="form-label">Operación</label>
                                    <select name="tipo_ajuste" id="tipo_ajuste" class="form-select" required>
                                        <option value="sumar" {{ old('tipo_ajuste') == 'sumar' ? 'selected' : '' }}>Aumentar (+)</option>
                                        <option value="restar" {{ old('tipo_ajuste') == 'restar' ? 'selected' : '' }}>Restar (-)</option>
                                        <option value="fijar" {{ old('tipo_ajuste') == 'fijar' ? 'selected' : '' }}>Fijar Total</option>
                                    </select>
                                    @error('tipo_ajuste')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-4 form-section">
                                    <label class="form-label">Cantidad</label>
                                    <input type="number" step="0.001" name="cantidad" class="form-control" placeholder="0.000" required min="0.001" value="{{ old('cantidad') }}">
                                    @error('cantidad')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-4 form-section">
                                    <label class="form-label">Motivo (opcional)</label>
                                    <input type="text" name="motivo" class="form-control" placeholder="Motivo del ajuste" value="{{ old('motivo') }}">
                                    @error('motivo')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary btn-sm">
                                    Volver
                                </a>
                                <button type="submit" class="btn btn-primary-clean btn-sm">
                                    <i class="fas fa-save me-1"></i> Guardar Ajuste
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipo = document.getElementById('tipo_ajuste');
            const helpText = document.createElement('small');
            helpText.className = 'text-muted mt-2 d-block';
            helpText.id = 'helpText';

            const cantidadInput = document.querySelector('input[name="cantidad"]');
            if (cantidadInput && cantidadInput.parentElement) {
                cantidadInput.parentElement.appendChild(helpText);
            }

            function updateHelp() {
                if (!tipo) return;
                if (tipo.value === 'sumar') {
                    helpText.innerText = 'Se sumará la cantidad indicada al stock actual.';
                } else if (tipo.value === 'restar') {
                    helpText.innerText = 'Se restará la cantidad indicada al stock actual.';
                } else {
                    helpText.innerText = 'El stock total pasará a ser exactamente la cantidad indicada.';
                }
            }

            if (tipo) {
                tipo.addEventListener('change', updateHelp);
                updateHelp();
            }
        });
    </script>
@endpush
