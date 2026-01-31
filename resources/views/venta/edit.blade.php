@extends('layouts.app')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #00b09b, #96c93d);
            --danger-gradient: linear-gradient(135deg, #ff416c, #ff4b2b);
            --warning-gradient: linear-gradient(135deg, #f59e0b, #fbbf24);
            --info-gradient: linear-gradient(135deg, #3498db, #2980b9);
            --border-color: #e5e7eb;
            --text-primary: #2c3e50;
            --text-secondary: #718096;
        }

        /* ========== ESTILOS COMPACTOS ========== */
        .container {
            max-width: 1400px;
            margin: 1rem auto;
            padding: 0 1rem;
        }

        h1 {
            color: var(--text-primary);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
            background: linear-gradient(135deg, var(--text-primary), #667eea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid;
            border-image: var(--primary-gradient) 1;
        }

        /* ========== SECCIONES COMPACTAS ========== */
        .border-section {
            background: white;
            border: none;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
        }

        .border-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
        }

        .section-title {
            background: var(--primary-gradient);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            margin: -20px -20px 20px -20px;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            font-size: 1.2rem;
        }

        /* ========== FORMULARIOS COMPACTOS ========== */
        .form-group {
            margin-bottom: 1rem;
        }

        label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
            font-size: 0.9rem;
            display: block;
        }

        .form-control {
            padding: 10px 12px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.15);
            outline: none;
        }

        .form-control:read-only {
            background-color: #f8f9fa;
            color: #6c757d;
        }

        /* ========== GRID COMPACTO ========== */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -8px;
        }

        .col-md-6,
        .col-md-4,
        .col-md-2 {
            padding: 0 8px;
            box-sizing: border-box;
            margin-bottom: 0.8rem;
        }

        .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }

        .col-md-4 {
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }

        .col-md-2 {
            flex: 0 0 16.666%;
            max-width: 16.666%;
        }

        /* ========== PRODUCTOS COMPACTOS ========== */
        .producto-row {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 15px;
            background: linear-gradient(135deg, #fafbfc, #f8f9fa);
            border-radius: 8px;
            border: 1px solid #f1f3f4;
            transition: all 0.3s ease;
            position: relative;
        }

        .producto-row:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .producto-info {
            position: absolute;
            top: -8px;
            right: 12px;
            background: var(--info-gradient);
            color: white;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .input-group {
            position: relative;
        }

        .input-group-text {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 6px 0 0 6px;
            font-weight: 600;
            min-width: 35px;
            justify-content: center;
            font-size: 0.8rem;
            padding: 0 8px;
        }

        .input-group .form-control {
            border-radius: 0 6px 6px 0;
            border-left: none;
        }

        /* ========== BOTONES COMPACTOS ========== */
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .btn-success {
            background: var(--success-gradient);
            color: white;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .btn-danger {
            background: var(--danger-gradient);
            color: white;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
            box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
        }

        .btn-sm {
            padding: 8px 12px;
            font-size: 0.8rem;
        }

        .btn i {
            font-size: 1rem;
        }

        /* ========== SELECT2 COMPACTO ========== */
        .select2-container--default .select2-selection--single {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            height: auto;
            padding: 10px;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.15);
        }

        /* ========== TOTAL COMPACTO ========== */
        .total-container {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            margin-top: 1.5rem;
        }

        .total-highlight {
            background: linear-gradient(135deg, #fff9c4, #fff176);
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 700;
            color: #7d6608;
            border-left: 3px solid #f59e0b;
            font-size: 1.1rem;
        }

        /* ========== BOTONES DE ACCIÓN ========== */
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 1.5rem;
            justify-content: flex-start;
        }

        /* ========== INFORMACIÓN DE STOCK ========== */
        .stock-info {
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            padding: 6px 10px;
            border-radius: 5px;
            font-weight: 600;
            color: #0c5460;
            border-left: 2px solid #17a2b8;
            margin-top: 4px;
            font-size: 0.8rem;
        }

        /* ========== RESPONSIVE COMPACTO ========== */
        @media (max-width: 1200px) {
            .producto-row {
                flex-wrap: wrap;
            }

            .col-md-4,
            .col-md-2 {
                flex: 0 0 50%;
                max-width: 50%;
                margin-bottom: 0.8rem;
            }
        }

        @media (max-width: 768px) {
            .container {
                margin: 0.5rem auto;
                padding: 0 0.8rem;
            }

            .border-section {
                padding: 15px;
            }

            .section-title {
                margin: -15px -15px 15px -15px;
                padding: 10px 15px;
                font-size: 1rem;
            }

            .col-md-6,
            .col-md-4,
            .col-md-2 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .producto-row {
                padding: 12px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            h1 {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .producto-row>div {
                margin-bottom: 0.8rem;
            }

            .remove-producto {
                width: 100%;
            }
        }

        /* ========== UTILIDADES COMPACTAS ========== */
        .text-muted {
            color: var(--text-secondary) !important;
            font-size: 0.8rem;
            margin-top: 3px;
            display: block;
        }

        .mt-4 {
            margin-top: 1.5rem;
        }

        .mt-3 {
            margin-top: 1rem;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        /* ========== ESTADOS DE VALIDACIÓN ========== */
        .is-invalid {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.1) !important;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <h1><i class="fas fa-edit me-2"></i>Editar Venta</h1>

        <form action="{{ route('ventas.update', $venta->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Sección de Datos Generales -->
            <div class="border-section">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Datos Generales
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="numero_comprobante"><i class="fas fa-receipt me-1"></i>N° Comprobante</label>
                            <input type="text" class="form-control" id="numero_comprobante" name="numero_comprobante"
                                value="{{ $venta->numero_comprobante }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_hora"><i class="fas fa-calendar me-1"></i>Fecha</label>
                            <input type="datetime-local" class="form-control" id="fecha_hora" name="fecha_hora"
                                value="{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('Y-m-d\TH:i') }}">
                        </div>
                    </div>
                </div>

                <!-- Campos ocultos requeridos -->
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="almacen_id"><i class="fas fa-warehouse me-1"></i>Sucursal/Almacén</label>
                            <select class="form-control" id="almacen_id" name="almacen_id" required>
                                <option value="">Seleccione almacén</option>
                                @foreach ($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}" {{ $venta->almacen_id == $almacen->id ? 'selected' : '' }}>
                                        {{ $almacen->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cliente_id"><i class="fas fa-user me-1"></i>Cliente</label>
                            <select class="form-control select2-search" id="cliente_id" name="cliente_id" required>
                                <option value="">Seleccione cliente</option>
                                @foreach ($clientes as $cliente)
                                    @if ($cliente->persona)
                                        <option value="{{ $cliente->id }}" @selected($venta->cliente_id == $cliente->id)>
                                            {{ $cliente->persona->razon_social ?? 'Cliente sin nombre' }}
                                            ({{ $cliente->persona->numero_documento ?? '' }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="comprobante_id"><i class="fas fa-file-invoice me-1"></i>Comprobante</label>
                            <select class="form-control" id="comprobante_id" name="comprobante_id" required>
                                @foreach ($comprobantes as $comprobante)
                                    <option value="{{ $comprobante->id }}"
                                        {{ $venta->comprobante_id == $comprobante->id ? 'selected' : '' }}>
                                        {{ $comprobante->tipo_comprobante }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Productos -->
            <div class="border-section">
                <div class="section-title">
                    <i class="fas fa-boxes"></i>
                    Productos de la Venta
                </div>

                <div class="card-body">
                    <div id="productos-container">
                        @foreach ($venta->detalles as $detalle)
                            <div class="producto-row">
                                <div class="col-md-4">
                                    <select class="form-control select2-producto" name="arrayidproducto[]" required>
                                        <option value="">Seleccione producto</option>
                                        @foreach ($productos as $prod)
                                            <option value="{{ $prod->id }}"
                                                {{ $detalle->producto_id == $prod->id ? 'selected' : '' }}
                                                data-precio="{{ $prod->precio_venta }}" data-stock="{{ $prod->stock }}">
                                                {{ $prod->nombre }} (Stock: {{ $prod->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Producto</small>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" step="0.0001" class="form-control cantidad"
                                        name="arraycantidad[]" value="{{ $detalle->cantidad }}" min="0.0001"
                                        required>
                                    <small class="text-muted">Cantidad</small>
                                </div>

                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control precio-venta"
                                            name="arrayprecioventa[]" value="{{ $detalle->precio_venta }}"
                                            required>
                                    </div>
                                    <small class="text-muted">Precio venta</small>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control descuento"
                                            name="arraydescuento[]" value="{{ $detalle->descuento ?? 0 }}">
                                    </div>
                                    <small class="text-muted">Descuento</small>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-producto">
                                        <i class="fas fa-trash me-1"></i>Eliminar
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" id="add-producto" class="btn btn-primary mt-3">
                        <i class="fas fa-plus-circle me-2"></i>Agregar Producto
                    </button>
                </div>
            </div>

            <!-- Sección del Total -->
            <div class="border-section">
                <div class="section-title">
                    <i class="fas fa-calculator"></i>
                    Total de la Venta
                </div>

                <div class="total-container">
                    <label for="total"><i class="fas fa-dollar-sign me-1"></i>Total de la Venta</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control total-highlight" id="total"
                            name="total" value="{{ $venta->total }}" readonly>
                    </div>
                </div>
            </div>

            <!-- Sección de Notas -->
            <div class="border-section">
                <div class="section-title">
                    <i class="fas fa-sticky-note"></i>
                    Notas
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nota_personal"><i class="fas fa-lock me-1"></i>Nota Interna</label>
                            <textarea class="form-control" id="nota_personal" name="nota_personal" rows="3" 
                                placeholder="Nota privada/interna (solo para el sistema)...">{{ $venta->nota_personal ?? '' }}</textarea>
                            <small class="text-muted">Esta nota solo es visible en el sistema</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nota_cliente"><i class="fas fa-user me-1"></i>Nota Cliente</label>
                            <textarea class="form-control" id="nota_cliente" name="nota_cliente" rows="3" 
                                placeholder="Nota para el cliente (se puede mostrar en documentos)...">{{ $venta->nota_cliente ?? '' }}</textarea>
                            <small class="text-muted">Esta nota puede ser visible para el cliente</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="action-buttons">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
                <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/es.js"></script>
    <!-- AGREGAR SWEETALERT2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            console.log('Documento cargado - Iniciando Select2');

            // Inicializar Select2 para clientes
            $('.select2-search').select2({
                placeholder: "Buscar cliente...",
                language: "es",
                width: '100%'
            });

            function actualizarPrecioYStock(selectElement, updatePrice = true) {
                const selectedValue = $(selectElement).val();
                if (!selectedValue) return;

                const originalOption = $(selectElement).find('option[value="' + selectedValue + '"]');
                const precio = originalOption.data('precio');
                const stock = originalOption.data('stock');

                console.log('Producto:', selectedValue, 'Precio:', precio, 'Stock:', stock);

                if (updatePrice && precio && precio !== '') {
                    $(selectElement).closest('.producto-row').find('.precio-venta').val(precio);
                } else {
                    console.log('Precio no actualizado (modo carga inicial o precio no disponible)');
                }

                if (stock) {
                    $(selectElement).closest('.producto-row').find('.cantidad').attr('max', stock);
                }
                calcularTotal();
            }

            // Inicializar Select2 para productos
            function initSelect2ForProducts() {
                $('.select2-producto').select2({
                    placeholder: "Buscar producto...",
                    language: "es",
                    width: '100%'
                }).on('change', function() {
                    actualizarPrecioYStock(this, true);
                });

                // Actualizar productos existentes
                $('.select2-producto').each(function() {
                    if ($(this).val()) {
                        actualizarPrecioYStock(this, false); // No sobrescribir precio al cargar
                    }
                });
            }

            // Inicializar Select2 para productos existentes
            initSelect2ForProducts();

            // Evitar productos repetidos
            $(document).on('change', '.select2-producto', function() {
                const seleccionActual = $(this).val();
                let repetido = false;

                $('.select2-producto').not(this).each(function() {
                    if ($(this).val() == seleccionActual && seleccionActual !== "") {
                        repetido = true;
                    }
                });

                if (repetido) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Producto duplicado',
                        text: 'Este producto ya fue agregado. No puedes agregarlo dos veces.',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#667eea',
                        background: '#fff'
                    });
                    $(this).val('').trigger('change');
                }
            });

            // Agregar nuevo producto
            $('#add-producto').click(function() {
                console.log('Agregando nuevo producto');

                const newRow = `
                    <div class="producto-row">
                        <div class="col-md-4">
                            <select class="form-control select2-producto" name="arrayidproducto[]" required>
                                <option value="">Seleccione producto</option>
                                @foreach ($productos as $prod)
                                    <option value="{{ $prod->id }}"
                                        data-precio="{{ $prod->precio_venta }}"
                                        data-stock="{{ $prod->stock }}">
                                        {{ $prod->nombre }} (Stock: {{ $prod->stock }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Producto</small>
                        </div>
                        <div class="col-md-2">
                            <input type="number" step="0.0001" class="form-control cantidad" name="arraycantidad[]" min="0.0001" required>
                            <small class="text-muted">Cantidad</small>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control precio-venta" name="arrayprecioventa[]" required>
                            </div>
                            <small class="text-muted">Precio venta</small>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control descuento" name="arraydescuento[]" value="0">
                            </div>
                            <small class="text-muted">Descuento</small>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm remove-producto">
                                <i class="fas fa-trash me-1"></i>Eliminar
                            </button>
                        </div>
                    </div>`;

                $('#productos-container').append(newRow);
                initSelect2ForProducts();
            });

            // Eliminar producto
            $(document).on('click', '.remove-producto', function() {
                $(this).closest('.producto-row').remove();
                calcularTotal();
            });

            // Calcular total cuando cambian cantidades, precios o descuentos
            $(document).on('input', '.cantidad, .precio-venta, .descuento', function() {
                calcularTotal();
            });

            function calcularTotal() {
                let total = 0;
                $('.producto-row').each(function() {
                    const cantidad = parseFloat($(this).find('.cantidad').val()) || 0;
                    const precio = parseFloat($(this).find('.precio-venta').val()) || 0;
                    const descuento = parseFloat($(this).find('.descuento').val()) || 0;
                    total += (cantidad * precio) - descuento;
                });
                $('#total').val(total.toFixed(2));
            }

            // Calcular total inicial
            setTimeout(function() {
                calcularTotal();
            }, 500);
        });
    </script>
@endpush
