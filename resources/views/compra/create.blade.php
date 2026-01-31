@extends('layouts.app')

@section('title', 'Realizar compra')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .border-section {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background: #fff;
        }
        .section-title {
            font-size: 15px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e9ecef;
        }
        .section-title i {
            margin-right: 8px;
            color: #3498db;
        }
        .form-label {
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 4px;
            color: #495057;
        }
        .form-control-sm {
            font-size: 13px;
            padding: 4px 8px;
            height: 30px;
        }
        .table th {
            font-size: 13px;
            padding: 10px 8px;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .table td {
            font-size: 13px;
            padding: 8px;
            vertical-align: middle;
        }
        .badge-modern {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 500;
        }
        .margen-bajo { background-color: #dc3545; color: white; }
        .margen-medio { background-color: #ffc107; color: #212529; }
        .margen-alto { background-color: #28a745; color: white; }
        .precio-highlight {
            color: #2c3e50;
            font-weight: 600;
        }
        .btn-sm {
            padding: 3px 8px;
            font-size: 12px;
        }
        .compact-row {
            margin-bottom: 0;
        }
        .compact-row > div {
            margin-bottom: 8px;
        }
        /* Search styles */
        .product-search-container {
            margin-bottom: 12px;
        }
        .search-wrapper {
            position: relative;
        }
        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 3;
        }
        #producto_search {
            padding-left: 35px;
            font-size: 13px;
            height: 32px;
        }
        .products-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .products-dropdown.show {
            display: block;
        }
        .product-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f1f1f1;
            transition: background 0.2s;
        }
        .product-item:hover {
            background: #f8f9fa;
        }
        .product-item.opacity-50 {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .product-info {
            flex: 1;
        }
        .product-name {
            font-weight: 500;
            font-size: 13px;
            color: #333;
        }
        .product-code {
            font-size: 11px;
            color: #6c757d;
        }
        .product-prices {
            display: flex;
            flex-direction: column;
            gap: 2px;
            font-size: 12px;
        }
        .product-price.compra {
            color: #28a745;
        }
        .product-price.venta {
            color: #dc3545;
        }
        .detail-inputs {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 15px;
        }
        .input-group-custom {
            display: flex;
            flex-direction: column;
        }
        .input-group-custom input {
            height: 32px;
            font-size: 13px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4 fs-4 fw-bold">Nueva Compra</h1>
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('compras.index') }}">Compras</a></li>
            <li class="breadcrumb-item active">Nueva Compra</li>
        </ol>
    </div>

    <form action="{{ route('compras.store') }}" method="post" id="compraForm">
        @csrf
        <input type="hidden" name="user_id" value="{{ auth()->id() }}">

        <div class="container-lg">
            <!-- Datos Generales -->
            <div class="border-section">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i> Datos Generales
                </div>
                <div class="row g-3 compact-row">
                    <!-- Proveedor -->
                    <div class="col-md-4">
                        <label for="proveedor_id" class="form-label">Proveedor:</label>
                        <select name="proveedor_id" id="proveedor_id" class="form-control form-control-sm selectpicker show-tick"
                            data-live-search="true" title="Seleccionar proveedor" data-size='5' required>
                            @foreach ($proveedores as $item)
                                <option value="{{ $item->id }}" {{ old('proveedor_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->persona->razon_social }}
                                </option>
                            @endforeach
                        </select>
                        @error('proveedor_id')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                    </div>

                    <!-- Sucursal -->
                    <div class="col-md-4">
                        <label for="almacen_id" class="form-label">Sucursal:</label>
                        <select name="almacen_id" id="almacen_id" class="form-control form-control-sm selectpicker"
                            title="Seleccionar sucursal" required>
                            @foreach ($almacenes as $item)
                                <option value="{{ $item->id }}" {{ old('almacen_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('almacen_id')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                    </div>

                    <!-- Comprobante -->
                    <div class="col-md-4">
                        <label for="comprobante_id" class="form-label">Comprobante:</label>
                        <select name="comprobante_id" id="comprobante_id" class="form-control form-control-sm selectpicker"
                            title="Tipo de comprobante">
                            @foreach ($comprobantes as $item)
                                <option value="{{ $item->id }}" {{ old('comprobante_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->tipo_comprobante }}
                                </option>
                            @endforeach
                        </select>
                        @error('comprobante_id')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                    </div>

                    <!-- Número Comprobante -->
                    <div class="col-md-3">
                        <label for="numero_comprobante" class="form-label">Número:</label>
                        <input type="text" name="numero_comprobante" id="numero_comprobante" class="form-control form-control-sm" 
                            value="{{ old('numero_comprobante', $nextComprobanteNumber ?? '00000001') }}" readonly>
                        @error('numero_comprobante')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                    </div>

                    <!-- Fecha -->
                    <div class="col-md-3">
                        <label for="fecha" class="form-label">Fecha:</label>
                        <input type="date" name="fecha" id="fecha" class="form-control form-control-sm"
                            value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="fecha_hora" value="{{ now()->toDateTimeString() }}">
                    </div>

                    <!-- Costo Transporte -->
                    <div class="col-md-3">
                        <label for="costo_transporte" class="form-label">Transporte:</label>
                        <input type="number" step="0.01" name="costo_transporte" id="costo_transporte" 
                            class="form-control form-control-sm" value="{{ old('costo_transporte', 0) }}">
                    </div>

                    <!-- Estado Entrega -->
                    <div class="col-md-3">
                        <label for="estado_entrega" class="form-label">Estado:</label>
                        <select name="estado_entrega" id="estado_entrega" class="form-control form-control-sm">
                            <option value="entregado">Entregado</option>
                            <option value="por_entregar">Por recibir</option>
                        </select>
                    </div>

                    <!-- Nota -->
                    <div class="col-12">
                        <label for="nota_personal" class="form-label">Notas:</label>
                        <textarea name="nota_personal" id="nota_personal" class="form-control form-control-sm" rows="1"
                            placeholder="Observaciones...">{{ old('nota_personal') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Detalles de Compra -->
            <div class="border-section">
                <div class="section-title">
                    <i class="fas fa-box"></i> Detalles de Compra
                </div>

                <!-- Buscador -->
                <div class="product-search-container">
                    <div class="search-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="producto_search" class="form-control form-control-sm"
                            placeholder="Buscar producto...">
                        <div class="products-dropdown" id="products_dropdown"></div>
                    </div>
                </div>

                <!-- Campos para agregar producto -->
                <div class="detail-inputs">
                    <div class="input-group-custom">
                        <label for="cantidad" class="form-label">Cantidad:</label>
                        <input type="number" id="cantidad" class="form-control form-control-sm" min="0.01" step="0.01"
                            value="1.00" placeholder="1.00">
                    </div>
                    <div class="input-group-custom">
                        <label for="precio_compra" class="form-label">P. Compra:</label>
                        <input type="number" id="precio_compra" class="form-control form-control-sm" step="0.01"
                            min="0.01" placeholder="0.00">
                    </div>
                    <div class="input-group-custom">
                        <label for="precio_venta" class="form-label">P. Venta:</label>
                        <input type="number" id="precio_venta" class="form-control form-control-sm" step="0.01" min="0.01"
                            placeholder="0.00">
                    </div>
                    <div class="input-group-custom">
                        <label for="margen" class="form-label">Margen:</label>
                        <input type="text" id="margen" disabled class="form-control form-control-sm" placeholder="0%">
                    </div>
                    <div class="input-group-custom d-flex align-items-end">
                        <button id="btn_agregar" class="btn btn-primary btn-sm w-100" type="button">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                </div>

                <!-- Tabla de detalles -->
                <div class="table-responsive">
                    <table id="tabla_detalle" class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="4%">#</th>
                                <th width="30%">Producto</th>
                                <th width="12%">Cantidad</th>
                                <th width="14%">P. Compra</th>
                                <th width="14%">P. Venta</th>
                                <th width="10%">Margen</th>
                                <th width="12%">Subtotal</th>
                                <th width="4%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Contenido dinámico -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-end fw-bold">TOTAL:</td>
                                <td colspan="2" class="fw-bold text-primary">
                                    <input type="hidden" name="total" value="0" id="inputTotal">
                                    <span id="total">Bs. 0.00</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Botones -->
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button id="cancelar" type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                        data-bs-target="#exampleModal" style="display: none;">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-sm" id="guardar" style="display: none;">
                        <i class="fas fa-check"></i> Registrar Compra
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal cancelar -->
        <div class="modal fade" id="exampleModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header bg-warning py-2">
                        <h6 class="modal-title">
                            <i class="fas fa-exclamation-triangle"></i> Confirmar
                        </h6>
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-2">
                        <small>¿Cancelar compra? Se perderán los datos.</small>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                        <button id="btnCancelarCompra" type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
    <script>
        $(document).ready(function() {
            // Datos y variables
            const productos = @json($productos);
            let productoSeleccionado = null;
            let cont = 0;
            let productosAgregados = new Set();

            // Inicializar
            $('.selectpicker').selectpicker();
            
            // Calcular margen
            $('#precio_compra, #precio_venta').on('input', calcularMargen);

            // Buscador de productos
            const searchInput = $('#producto_search');
            const dropdown = $('#products_dropdown');

            searchInput.on('input', function() {
                const searchTerm = $(this).val().toLowerCase().trim();
                if (searchTerm.length === 0) {
                    dropdown.removeClass('show').empty();
                    return;
                }

                const filtered = productos.filter(p => {
                    const nombre = p.nombre.toLowerCase();
                    const codigo = p.codigo.toLowerCase();
                    return nombre.includes(searchTerm) || codigo.includes(searchTerm);
                });

                renderDropdown(filtered);
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.product-search-container').length) {
                    dropdown.removeClass('show');
                }
            });

            // Agregar producto
            $('#btn_agregar').click(agregarProducto);
            $('#btnCancelarCompra').click(cancelarCompra);

            // Edición en línea
            $(document).on("input", ".cantidad-input, .compra-input, .venta-input", function() {
                const fila = $(this).closest('tr');
                actualizarFila(fila);
            });

            // Eliminar producto
            $(document).on("click", ".btn-eliminar", function() {
                const fila = $(this).closest("tr");
                const id = parseInt(fila.data("id"));
                productosAgregados.delete(id);
                fila.remove();
                renumerarFilas();
                calcularTotales();
                mostrarBotones();
            });

            // Validar formulario
            $('#compraForm').submit(function(e) {
                if ($('#tabla_detalle tbody tr').length === 0) {
                    e.preventDefault();
                    Swal.fire("Error", "Agregue al menos un producto", "error");
                    return false;
                }

                // Validar que no haya precios de venta menores o iguales a compra
                let error = false;
                $('.venta-input').each(function() {
                    const compra = parseFloat($(this).closest('tr').find('.compra-input').val());
                    const venta = parseFloat($(this).val());
                    if (venta <= compra) {
                        $(this).addClass('is-invalid');
                        error = true;
                    }
                });

                if (error) {
                    e.preventDefault();
                    Swal.fire("Error", "Revise los precios de venta", "error");
                    return false;
                }

                return true;
            });

            // ========== FUNCIONES ==========

            function renderDropdown(products) {
                dropdown.empty();

                if (products.length === 0) {
                    dropdown.html('<div class="p-2 text-muted small">No se encontraron productos</div>');
                    dropdown.addClass('show');
                    return;
                }

                products.forEach(product => {
                    const yaAgregado = productosAgregados.has(product.id);
                    const item = $(`
                        <div class="product-item ${yaAgregado ? 'opacity-50' : ''}" data-id="${product.id}">
                            <div class="d-flex justify-content-between">
                                <div class="product-info">
                                    <div class="product-name">${product.nombre}</div>
                                    <div class="product-code">${product.codigo}</div>
                                </div>
                                <div class="product-prices">
                                    <div class="product-price compra">Compra: ${product.precio_compra}</div>
                                    <div class="product-price venta">Venta: ${product.precio_venta}</div>
                                </div>
                            </div>
                        </div>
                    `);

                    if (!yaAgregado) {
                        item.on('click', function() {
                            selectProduct(product);
                            dropdown.removeClass('show');
                            $('#producto_search').val('');
                        });
                    }

                    dropdown.append(item);
                });

                dropdown.addClass('show');
            }

            function selectProduct(product) {
                productoSeleccionado = product;
                $('#precio_compra').val(parseFloat(product.precio_compra).toFixed(2));
                $('#precio_venta').val(parseFloat(product.precio_venta).toFixed(2));
                $('#cantidad').val('1.00').focus();
                calcularMargen();
                $('#producto_search').val(`${product.codigo} - ${product.nombre}`);
            }

            function calcularMargen() {
                const compra = parseFloat($('#precio_compra').val()) || 0;
                const venta = parseFloat($('#precio_venta').val()) || 0;
                
                if (compra > 0 && venta > 0) {
                    const margen = ((venta - compra) / compra * 100).toFixed(2);
                    const margenElement = $('#margen');
                    margenElement.val(margen + '%');
                    margenElement.removeClass('margen-bajo margen-medio margen-alto');
                    
                    if (margen < 10) {
                        margenElement.addClass('margen-bajo');
                    } else if (margen < 30) {
                        margenElement.addClass('margen-medio');
                    } else {
                        margenElement.addClass('margen-alto');
                    }
                } else {
                    $('#margen').val('0%').removeClass('margen-bajo margen-medio margen-alto');
                }
            }

            function agregarProducto() {
                if (!productoSeleccionado) {
                    Swal.fire("Error", "Seleccione un producto", "error");
                    return;
                }

                const id = productoSeleccionado.id;
                if (productosAgregados.has(id)) {
                    Swal.fire("Advertencia", "Producto ya agregado", "warning");
                    return;
                }

                const cantidad = parseFloat($('#cantidad').val()) || 0;
                const compra = parseFloat($('#precio_compra').val()) || 0;
                const venta = parseFloat($('#precio_venta').val()) || 0;

                // Validaciones
                if (cantidad <= 0 || compra <= 0 || venta <= 0) {
                    Swal.fire("Error", "Complete todos los campos", "error");
                    return;
                }

                if (venta <= compra) {
                    Swal.fire("Error", "Precio venta debe ser mayor", "error");
                    return;
                }

                // Agregar fila
                cont++;
                const subtotal = (cantidad * compra).toFixed(2);
                const margenBadge = calcularBadgeMargen(compra, venta);

                const fila = `
                    <tr id="fila_${cont}" data-id="${id}">
                        <td>${cont}</td>
                        <td class="small">
                            <div class="fw-bold">${productoSeleccionado.nombre}</div>
                            <small class="text-muted">${productoSeleccionado.codigo}</small>
                            <input type="hidden" name="arrayidproducto[]" value="${id}">
                        </td>
                        <td>
                            <input type="number" name="arraycantidad[]" 
                                   class="form-control form-control-sm cantidad-input" 
                                   value="${cantidad.toFixed(2)}" min="0.01" step="0.01" required>
                        </td>
                        <td>
                            <input type="number" name="arraypreciocompra[]" 
                                   class="form-control form-control-sm compra-input" 
                                   value="${compra.toFixed(2)}" min="0.01" step="0.01" required>
                        </td>
                        <td>
                            <input type="number" name="arrayprecioventa[]" 
                                   class="form-control form-control-sm venta-input" 
                                   value="${venta.toFixed(2)}" min="0.01" step="0.01" required>
                        </td>
                        <td class="margen-cell">${margenBadge}</td>
                        <td class="subtotal fw-bold">${subtotal}</td>
                        <td>
                            <button type="button" class="btn btn-outline-danger btn-sm btn-eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $('#tabla_detalle tbody').append(fila);
                productosAgregados.add(id);
                limpiarCampos();
                calcularTotales();
                mostrarBotones();
            }

            function actualizarFila(fila) {
                const cantidad = parseFloat(fila.find('.cantidad-input').val()) || 0;
                const compra = parseFloat(fila.find('.compra-input').val()) || 0;
                const venta = parseFloat(fila.find('.venta-input').val()) || 0;

                // Validar venta > compra
                if (venta <= compra) {
                    fila.find('.venta-input').addClass('is-invalid');
                    return;
                } else {
                    fila.find('.venta-input').removeClass('is-invalid');
                }

                // Actualizar margen
                const margenBadge = calcularBadgeMargen(compra, venta);
                fila.find('.margen-cell').html(margenBadge);

                // Actualizar subtotal
                const subtotal = (cantidad * compra).toFixed(2);
                fila.find('.subtotal').text(subtotal);

                calcularTotales();
            }

            function calcularBadgeMargen(compra, venta) {
                const margen = ((venta - compra) / compra * 100).toFixed(2);
                let clase = 'margen-bajo';
                if (margen >= 30) clase = 'margen-alto';
                else if (margen >= 10) clase = 'margen-medio';
                
                return `<span class="badge-modern ${clase}">${margen}%</span>`;
            }

            function calcularTotales() {
                let total = 0;
                $('.subtotal').each(function() {
                    total += parseFloat($(this).text()) || 0;
                });
                $('#total').text('S/ ' + total.toFixed(2));
                $('#inputTotal').val(total.toFixed(2));
            }

            function renumerarFilas() {
                $('#tabla_detalle tbody tr').each(function(index) {
                    $(this).find('td').first().text(index + 1);
                });
                cont = $('#tabla_detalle tbody tr').length;
            }

            function cancelarCompra() {
                $('#tabla_detalle tbody').empty();
                productosAgregados.clear();
                calcularTotales();
                limpiarCampos();
                $('#guardar, #cancelar').fadeOut();
            }

            function limpiarCampos() {
                productoSeleccionado = null;
                $('#producto_search').val('');
                $('#cantidad').val('1.00');
                $('#precio_compra').val('');
                $('#precio_venta').val('');
                $('#margen').val('0%').removeClass('margen-bajo margen-medio margen-alto');
            }

            function mostrarBotones() {
                if ($('#tabla_detalle tbody tr').length > 0) {
                    $('#guardar, #cancelar').fadeIn();
                } else {
                    $('#guardar, #cancelar').fadeOut();
                }
            }
        });
    </script>
@endpush