@extends('layouts.app')

@section('title', 'Crear Traslado')

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .border-section {
            border: 2px solid #e3f2fd;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            background-color: #f8fbff;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid #667eea;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: #667eea;
            font-size: 1.3rem;
        }

        .form-label {
            font-weight: 600;
            color: #34495e;
            margin-bottom: 0.5rem;
        }

        .form-control, .selectpicker {
            border-radius: 6px;
            border: 1px solid #bdc3c7;
            padding: 0.6rem 0.8rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .selectpicker:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .detail-inputs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background-color: #fff;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .input-group-custom {
            display: flex;
            flex-direction: column;
        }

        .with-icon {
            padding-left: 2.5rem;
        }

        .table-detalle {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table-detalle thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table-detalle th, .table-detalle td {
            padding: 0.8rem;
            text-align: left;
            border-bottom: 1px solid #ecf0f1;
        }

        .table-detalle tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn {
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-danger {
            background-color: #e74c3c;
            border: none;
        }

        .btn-danger:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 6px;
            border: none;
        }

        .product-search-container {
            margin-bottom: 1.5rem;
        }

        .search-wrapper {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
        }

        .products-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 6px 6px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .products-dropdown.show {
            display: block;
        }

        .product-item {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #ecf0f1;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .product-item:hover {
            background-color: #f8f9fa;
            padding-left: 1.2rem;
        }

        .product-item-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
        }

        .product-item-code {
            color: #7f8c8d;
            font-size: 0.85rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Crear Traslado</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('traslados.index') }}">Traslados</a></li>
            <li class="breadcrumb-item active">Crear Traslado</li>
        </ol>
    </div>

    <form action="{{ route('traslados.store') }}" method="post" id="trasladoForm">
        @csrf
        <div class="container-lg mt-4">
            <!-- Sección de Datos Generales -->
            <div class="border-section">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Datos Generales del Traslado
                </div>
                <div class="row">
                    <!-- Almacén Origen -->
                    <div class="col-md-6 mb-3">
                        <label for="origen_almacen_id" class="form-label">Almacén Origen:</label>
                        <select name="origen_almacen_id" id="origen_almacen_id" class="form-control" required>
                            <option value="">Seleccione almacén origen</option>
                            @foreach ($almacenes as $almacen)
                                <option value="{{ $almacen->id }}" {{ old('origen_almacen_id') == $almacen->id ? 'selected' : '' }}>
                                    {{ $almacen->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('origen_almacen_id')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                    </div>

                    <!-- Almacén Destino -->
                    <div class="col-md-6 mb-3">
                        <label for="destino_almacen_id" class="form-label">Almacén Destino:</label>
                        <select name="destino_almacen_id" id="destino_almacen_id" class="form-control" required>
                            <option value="">Seleccione almacén destino</option>
                            @foreach ($almacenes as $almacen)
                                <option value="{{ $almacen->id }}" {{ old('destino_almacen_id') == $almacen->id ? 'selected' : '' }}>
                                    {{ $almacen->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('destino_almacen_id')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                    </div>

                    <!-- Fecha y Hora -->
                    <div class="col-md-6 mb-3">
                        <label for="fecha_hora" class="form-label">Fecha y Hora:</label>
                        <input type="datetime-local" id="fecha_hora_display" class="form-control" 
                            disabled value="{{ now()->format('Y-m-d\TH:i') }}">
                        <input type="hidden" name="fecha_hora" id="fecha_hora" 
                            value="{{ now()->format('Y-m-d\TH:i:s') }}">
                        @error('fecha_hora')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                    </div>

                    <!-- Costo de Envío -->
                    <div class="col-md-6 mb-3">
                        <label for="costo_envio" class="form-label">Costo de Envío:</label>
                        <input type="number" name="costo_envio" id="costo_envio" class="form-control" 
                            min="0" step="0.01" placeholder="0.00" value="{{ old('costo_envio', '0.00') }}" required>
                        @error('costo_envio')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                    </div>

                    <!-- Usuario (oculto) -->
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                </div>
            </div>

            <!-- Sección de Productos -->
            <div class="border-section">
                <div class="section-title">
                    <i class="fas fa-boxes"></i>
                    Productos del Traslado
                </div>

                <!-- Buscador de Productos -->
                <div class="product-search-container">
                    <div class="search-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="producto_search" class="form-control"
                               placeholder="Buscar producto por nombre o código...">
                        <div class="products-dropdown" id="products_dropdown">
                            <!-- Contenido dinámico -->
                        </div>
                    </div>
                </div>

                <!-- Campos de Detalle -->
                <div class="detail-inputs">
                    <!-- Stock -->
                    <div class="input-group-custom">
                        <label for="stock" class="form-label">Stock Disponible:</label>
                        <input disabled id="stock" type="text" class="form-control" placeholder="0.0000">
                    </div>

                    <!-- Cantidad -->
                    <div class="input-group-custom">
                        <label for="cantidad" class="form-label">Cantidad:</label>
                        <input type="number" id="cantidad" class="form-control" 
                            min="0.001" step="0.001" value="1.000" placeholder="1.000">
                    </div>

                    <!-- Botón Agregar -->
                    <div>
                        <label class="form-label" style="visibility: hidden;">Acción</label>
                        <button id="btn_agregar" class="btn btn-primary w-100" type="button">
                            <i class="fas fa-plus-circle"></i> Agregar
                        </button>
                    </div>
                </div>

                <!-- Tabla para el detalle del traslado -->
                <div class="table-responsive">
                    <table id="tabla_detalle" class="table-detalle">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="35%">Producto</th>
                                <th width="20%">Cantidad</th>
                                <th width="20%">Stock</th>
                                <th width="20%">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_detalle">
                        </tbody>
                    </table>
                </div>

                <!-- Mensaje si no hay productos -->
                <div id="sin_productos" class="alert alert-info text-center mt-3" style="display: none;">
                    <i class="fas fa-info-circle"></i> No hay productos agregados aún
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="row mt-4 mb-4">
                <div class="col-md-6">
                    <a href="{{ route('traslados.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Guardar Traslado
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script src="{{ asset('js/traslados.js') }}"></script>
@endpush
