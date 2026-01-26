@extends('layouts.app')
@push('css')
    <style>
        /* Estilos generales */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            font-size: 2rem;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
        }

        h4 {
            color: #2c3e50;
            margin: 0;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -0.75rem 1rem;
        }

        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 0.75rem;
            box-sizing: border-box;
        }

        .col-md-4,
        .col-md-2 {
            padding: 0 0.75rem;
            box-sizing: border-box;
        }

        .col-md-4 {
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }

        .col-md-2 {
            flex: 0 0 16.666%;
            max-width: 16.666%;
        }

        /* Estilos de los campos del formulario */
        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #34495e;
            font-weight: 600;
        }

        input[type="text"],
        input[type="number"],
        input[type="datetime-local"],
        select,
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="datetime-local"]:focus,
        select:focus,
        .form-control:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        input[readonly] {
            background-color: #f5f5f5;
        }

        /* Estilos de la sección de productos */
        .card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .card-header {
            background-color: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e0e0e0;
        }

        .card-body {
            padding: 1.5rem;
        }

        .producto-row {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        /* Estilos de botones */
        button {
            cursor: pointer;
            padding: 0.75rem 1.25rem;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .btn-secondary {
            background-color: #95a5a6;
            color: white;
            margin-left: 0.5rem;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
            padding: 0.5rem 1rem;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        /* Estilos para el total */
        #total {
            font-size: 1.25rem;
            font-weight: bold;
            padding: 1rem;
            background-color: #f8f9fa;
        }

        /* Estilos para los botones de acción */
        .mt-4 {
            margin-top: 1.5rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {

            .col-md-6,
            .col-md-4,
            .col-md-2 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 1rem;
            }

            .producto-row {
                flex-wrap: wrap;
            }

            .producto-row>div {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 0.5rem;
            }

            .producto-row .btn-danger {
                width: 100%;
            }
        }

        /* Estilos adicionales para compras */
        .input-group-text {
            min-width: 40px;
            justify-content: center;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <h1>Editar Compra</h1>

        <form action="{{ route('compras.update', $compra->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="numero_comprobante">N° Comprobante</label>
                        <input type="text" class="form-control" id="numero_comprobante" name="numero_comprobante"
                            value="{{ $compra->numero_comprobante }}" readonly>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="fecha_hora">Fecha</label>
                        <input type="datetime-local" class="form-control" id="fecha_hora" name="fecha_hora"
                            value="{{ \Carbon\Carbon::parse($compra->fecha_hora)->format('Y-m-d\TH:i') }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="proveedor_id">Proveedor</label>
                        <select class="form-control select2-search" id="proveedor_id" name="proveedor_id" required>
                            <option value="">Seleccione proveedor</option>
                            @foreach ($proveedores as $proveedor)
                                @if ($proveedor->persona)
                                    <option value="{{ $proveedor->id }}" @selected($compra->proveedor_id == $proveedor->id)>
                                        {{ $proveedor->persona->razon_social ?? 'Proveedor sin nombre' }}
                                        ({{ $proveedor->persona->numero_documento ?? '' }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="almacen_id">Sucursal / Almacén</label>
                        <select class="form-control" id="almacen_id" name="almacen_id" required>
                            @foreach ($almacenes as $almacen)
                                <option value="{{ $almacen->id }}" @selected($compra->almacen_id == $almacen->id)>
                                    {{ $almacen->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="comprobante_id">Comprobante</label>
                        <select class="form-control" id="comprobante_id" name="comprobante_id" required>
                            @foreach ($comprobantes as $comprobante)
                                <option value="{{ $comprobante->id }}"
                                    {{ $compra->comprobante_id == $comprobante->id ? 'selected' : '' }}>
                                    {{ $comprobante->tipo_comprobante }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Sección de productos -->
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Productos</h4>
                </div>
                <div class="card-body">
                    <div id="productos-container">
                        @foreach ($compra->detalles as $detalle)
                            @php
                                $producto = $detalle->producto;
                                $productoInfo = $productosInfo[$producto->id] ?? null;
                                $vendido = $productoInfo['vendido'] ?? 0;
                                $minimoPermitido = $productoInfo['minimo_permitido'] ?? 0;
                                // Formatear valores para mostrar
                                $cantidadFormateada = number_format($detalle->cantidad, 2, '.', '');
                                $precioCompraFormateado = number_format($detalle->precio_compra, 2, '.', '');
                                $precioVentaFormateado = number_format($detalle->precio_venta, 2, '.', '');
                            @endphp

                            <div class="row producto-row mb-3">
                                <div class="col-md-4">
                                    <select class="form-control select2-producto" name="arrayidproducto[]" required>
                                        <option value="">Seleccione producto</option>
                                        @foreach ($productos as $prod)
                                            @php
                                                $prodStockFormateado = number_format($prod->stock, 2, '.', '');
                                            @endphp
                                            <option value="{{ $prod->id }}"
                                                {{ $producto->id == $prod->id ? 'selected' : '' }}
                                                data-precio-venta="{{ number_format($prod->precio_venta, 2, '.', '') }}"
                                                data-precio-compra="{{ number_format($prod->precio_compra, 2, '.', '') }}"
                                                data-stock="{{ $prodStockFormateado }}">
                                                {{ $prod->nombre }} (Stock: {{ $prodStockFormateado }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Seleccione el producto</small>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control cantidad" name="arraycantidad[]"
                                        value="{{ $cantidadFormateada }}" step="0.001" min="0.001" required>
                                    <small class="text-muted">Cantidad</small>

                                    <!-- Mostrar información de ventas si existe -->
                                    @if ($productoInfo)
                                        <small class="text-danger d-block">
                                            Vendido: {{ number_format($vendido, 2, '.', '') }}
                                        </small>
                                        <small class="text-warning d-block">
                                            Mínimo: {{ number_format($minimoPermitido, 2, '.', '') }}
                                        </small>
                                    @endif
                                    
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control precio-compra"
                                            name="arraypreciocompra[]" value="{{ $precioCompraFormateado }}" min="0.01"
                                            required>
                                    </div>
                                    <small class="text-muted">Precio compra</small>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control precio-venta"
                                            name="arrayprecioventa[]" value="{{ $precioVentaFormateado }}" min="0.01"
                                            required>
                                    </div>
                                    <small class="text-muted">Precio venta</small>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger remove-producto">Eliminar</button>
                                    <div class="mt-1"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-producto" class="btn btn-primary mt-3">Agregar Producto</button>
                </div>
            </div>

            <div class="form-group mt-4">
                <label for="total">Total</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control" id="total" name="total"
                        value="{{ number_format($compra->total, 2, '.', '') }}" readonly>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <a href="{{ route('compras.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar Select2 para proveedores
            $('.select2-search').select2({
                placeholder: "Buscar proveedor...",
                language: "es",
                width: '100%'
            });

            // Función para inicializar Select2 en productos
            function initSelect2ForProducts() {
                $('.select2-producto').select2({
                    placeholder: "Buscar producto...",
                    language: "es",
                    width: '100%'
                }).on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const precioVenta = parseFloat(selectedOption.data('precio-venta')) || 0;
                    const precioCompra = parseFloat(selectedOption.data('precio-compra')) || 0;

                    // Actualizar ambos precios solamente
                    $(this).closest('.producto-row').find('.precio-compra').val(precioCompra.toFixed(2));
                    $(this).closest('.producto-row').find('.precio-venta').val(precioVenta.toFixed(2));
                    calcularTotal();
                });
            }

            // Inicializar para productos existentes
            initSelect2ForProducts();

            // Agregar nuevo producto
            $('#add-producto').click(function() {
                const newRow = `
                    <div class="row producto-row mb-3">
                        <div class="col-md-4">
                            <select class="form-control select2-producto" name="arrayidproducto[]" required>
                                <option value="">Seleccione producto</option>
                                @foreach ($productos as $prod)
                                    @php
                                        $prodStockFormateado = number_format($prod->stock, 2, '.', '');
                                    @endphp
                                    <option value="{{ $prod->id }}"
                                        data-precio-venta="{{ number_format($prod->precio_venta, 2, '.', '') }}"
                                        data-precio-compra="{{ number_format($prod->precio_compra, 2, '.', '') }}"
                                        data-stock="{{ $prodStockFormateado }}">
                                        {{ $prod->nombre }} (Stock: {{ $prodStockFormateado }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Seleccione el producto</small>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control cantidad" name="arraycantidad[]"
                                step="0.01" min="0.01" value="1.00" required>
                            <small class="text-muted">Cantidad</small>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control precio-compra"
                                    name="arraypreciocompra[]" min="0.01" value="0.00" required>
                            </div>
                            <small class="text-muted">Precio compra</small>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control precio-venta"
                                    name="arrayprecioventa[]" min="0.01" value="0.00" required>
                            </div>
                            <small class="text-muted">Precio venta</small>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-producto">Eliminar</button>
                            <div class="mt-1"></div>
                        </div>
                    </div>`;

                $('#productos-container').append(newRow);
                initSelect2ForProducts();
                calcularTotal();
            });

            // Eliminar producto
            $(document).on('click', '.remove-producto', function() {
                // No permitir eliminar si solo queda un producto
                if ($('.producto-row').length <= 1) {
                    alert('Debe haber al menos un producto en la compra');
                    return;
                }
                $(this).closest('.producto-row').remove();
                calcularTotal();
            });

            // Calcular total cuando cambian cantidades o precios
            $(document).on('input', '.cantidad, .precio-compra', function() {
                calcularTotal();
            });

            function calcularTotal() {
                let total = 0;
                $('.producto-row').each(function() {
                    const cantidad = parseFloat($(this).find('.cantidad').val()) || 0;
                    const precio = parseFloat($(this).find('.precio-compra').val()) || 0;
                    if (!isNaN(cantidad) && !isNaN(precio)) {
                        total += cantidad * precio;
                    }
                });
                $('#total').val(total.toFixed(2));
            }

            // Calcular total inicial
            calcularTotal();
        });
    </script>
@endpush
