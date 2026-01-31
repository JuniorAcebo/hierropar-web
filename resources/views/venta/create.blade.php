@extends('layouts.app')

@section('title', 'Realizar venta')

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
            font-size: 16px;
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
        .stock-warning {
            color: #dc3545;
            font-weight: 500;
        }
        .stock-normal {
            color: #28a745;
            font-weight: 500;
        }
        .precio-highlight {
            color: #2c3e50;
            font-weight: 600;
        }
        .btn-sm {
            padding: 3px 8px;
            font-size: 12px;
        }
        .product-info-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 12px;
            margin-bottom: 12px;
        }
        .product-info-title {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
        }
        .compact-row {
            margin-bottom: 0;
        }
        .compact-row > div {
            margin-bottom: 8px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4 fs-4 fw-bold">Realizar Venta</h1>
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
            <li class="breadcrumb-item active">Nueva Venta</li>
        </ol>
    </div>

    <form action="{{ route('ventas.store') }}" method="post" id="ventaForm">
        @csrf
        <div class="container-lg">
            <!-- Sección de Datos Generales -->
            <div class="border-section">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i> Datos Generales
                </div>
                <div class="row g-3 compact-row">
                    <!--Cliente-->
                    <div class="col-md-6">
                        <label for="cliente_id" class="form-label">Cliente:</label>
                        <select name="cliente_id" id="cliente_id" class="form-control form-control-sm selectpicker show-tick"
                            data-live-search="true" title="Seleccione un cliente" data-size='5' required>
                            @foreach ($clientes as $item)
                                <option value="{{ $item->id }}">{{ $item->persona->razon_social }}</option>
                            @endforeach
                        </select>
                        @error('cliente_id')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                    </div>

                    <!--Sucursal / Almacen-->
                    <div class="col-md-6">
                        <label for="almacen_id" class="form-label">Sucursal:</label>
                        <select name="almacen_id" id="almacen_id" class="form-control form-control-sm selectpicker"
                            title="Seleccione sucursal" required>
                            @foreach ($almacenes as $item)
                                <option value="{{ $item->id }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $item->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('almacen_id')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                    </div>

                    <!--Tipo de comprobante-->
                    <div class="col-md-4">
                        <label for="comprobante_id" class="form-label">Comprobante:</label>
                        <select name="comprobante_id" id="comprobante_id" class="form-control form-control-sm selectpicker"
                            title="Seleccione tipo" required>
                            @foreach ($comprobantes as $item)
                                <option value="{{ $item->id }}">{{ $item->tipo_comprobante }}</option>
                            @endforeach
                        </select>
                        @error('comprobante_id')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                    </div>

                    <!--Numero de comprobante-->
                    <div class="col-md-2">
                        <label for="numero_comprobante" class="form-label">Número:</label>
                        <input type="text" name="numero_comprobante" id="numero_comprobante" 
                               class="form-control form-control-sm" value="{{ $nextComprobanteNumber }}" readonly>
                    </div>

                    <!--Fecha-->
                    <div class="col-md-3">
                        <label for="fecha" class="form-label">Fecha:</label>
                        <input readonly type="date" name="fecha" id="fecha" class="form-control form-control-sm"
                            value="{{ now()->format('Y-m-d') }}">
                        <input type="hidden" name="fecha_hora" value="{{ now()->toDateTimeString() }}">
                    </div>

                    <!--User-->
                    <div class="col-md-3">
                        <label class="form-label">Vendedor:</label>
                        <input type="text" class="form-control form-control-sm" value="{{ auth()->user()->name }}" readonly>
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    </div>
                </div>
            </div>

            <!-- Sección de Detalles de Venta -->
            <div class="border-section">
                <div class="section-title">
                    <i class="fas fa-shopping-cart"></i> Detalles de la Venta
                </div>

                <!-- Selección de Producto -->
                <div class="row g-2 mb-3 compact-row">
                    <div class="col-md-9">
                        <label for="producto_select" class="form-label">Producto:</label>
                        <select id="producto_select" class="form-control form-control-sm selectpicker show-tick" 
                                data-live-search="true" title="Buscar producto..." data-size="5">
                            @foreach ($productos as $producto)
                                <option value="{{ $producto->id }}" 
                                        data-codigo="{{ $producto->codigo }}"
                                        data-nombre="{{ $producto->nombre }}"
                                        data-precio="{{ $producto->precio_venta }}">
                                    {{ $producto->codigo }} - {{ $producto->nombre }} 
                                    (S/{{ number_format($producto->precio_venta, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" id="btn_seleccionar_producto" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-search"></i> Seleccionar
                        </button>
                    </div>
                </div>

                <!-- Información del Producto Seleccionado -->
                <div class="product-info-card" id="producto_info" style="display: none;">
                    <div class="product-info-title">
                        <i class="fas fa-box"></i> Producto Seleccionado
                    </div>
                    <div class="row g-2 compact-row">
                        <div class="col-md-3">
                            <input type="text" id="producto_nombre" class="form-control form-control-sm" placeholder="Producto" readonly>
                            <input type="hidden" id="producto_id">
                        </div>
                        <div class="col-md-2">
                            <input type="text" id="producto_codigo" class="form-control form-control-sm" placeholder="Código" readonly>
                        </div>
                        <div class="col-md-2">
                            <input type="text" id="producto_stock" class="form-control form-control-sm" placeholder="Stock" readonly>
                        </div>
                        <div class="col-md-2">
                            <input type="number" id="producto_precio" class="form-control form-control-sm" 
                                   placeholder="Precio" step="0.01" min="0.01">
                        </div>
                        <div class="col-md-2">
                            <input type="number" id="producto_cantidad" class="form-control form-control-sm" 
                                   value="1.000" placeholder="Cantidad" step="0.001" min="0.001">
                        </div>
                        <div class="col-md-1">
                            <button type="button" id="btn_agregar_producto" class="btn btn-success btn-sm w-100">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de detalles -->
                <div class="table-responsive mt-3">
                    <table id="tabla_detalle" class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="4%">#</th>
                                <th width="30%">Producto</th>
                                <th width="10%">Cantidad</th>
                                <th width="12%">P. Venta</th>
                                <th width="12%">Descuento</th>
                                <th width="10%">Stock</th>
                                <th width="12%">Subtotal</th>
                                <th width="5%"></th>
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
                                    <span id="total">S/ 0.00</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Nota Personal -->
                <div class="row mt-3 compact-row">
                    <div class="col-md-6">
                        <label for="nota_personal" class="form-label">Nota Interna:</label>
                        <textarea name="nota_personal" id="nota_personal" class="form-control form-control-sm" rows="2" 
                                  placeholder="Nota personal (opcional)..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="nota_cliente" class="form-label">Nota Cliente:</label>
                        <textarea name="nota_cliente" id="nota_cliente" class="form-control form-control-sm" rows="2" 
                                  placeholder="Nota para el cliente (opcional)..."></textarea>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button id="cancelar" type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                        data-bs-target="#exampleModal" style="display: none;">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success btn-sm" id="guardar" style="display: none;">
                        <i class="fas fa-check"></i> Realizar Venta
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
                        <small>¿Cancelar venta? Se perderán los datos.</small>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                        <button id="btnCancelarVenta" type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
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
            // Inicializar selectpickers
            $('.selectpicker').selectpicker();
            
            // Variables globales
            let cont = 0;
            let productosAgregados = new Set();
            let productoActual = null;
            
            // Seleccionar producto
            $('#btn_seleccionar_producto').click(function() {
                const productoSelect = $('#producto_select');
                const productoId = productoSelect.val();
                
                if (!productoId) {
                    Swal.fire("Error", "Seleccione un producto", "error");
                    return;
                }
                
                const almacenId = $('#almacen_id').val();
                if (!almacenId) {
                    Swal.fire("Error", "Seleccione una sucursal", "error");
                    return;
                }
                
                const productoOption = productoSelect.find('option:selected');
                const productoNombre = productoOption.data('nombre');
                const productoCodigo = productoOption.data('codigo');
                const productoPrecio = parseFloat(productoOption.data('precio')) || 0;
                
                // Verificar stock
                $.ajax({
                    url: '{{ route("ventas.check-stock") }}',
                    method: 'GET',
                    data: {
                        producto_id: productoId,
                        almacen_id: almacenId
                    },
                    success: function(response) {
                        if (response.success) {
                            const stock = parseFloat(response.stock);
                            
                            $('#producto_info').fadeIn();
                            $('#producto_id').val(productoId);
                            $('#producto_nombre').val(productoNombre);
                            $('#producto_codigo').val(productoCodigo);
                            $('#producto_stock').val(stock.toFixed(4));
                            $('#producto_precio').val(productoPrecio.toFixed(2));
                            $('#producto_cantidad').val('1.000').focus();
                            
                            productoActual = {
                                id: parseInt(productoId),
                                nombre: productoNombre,
                                codigo: productoCodigo,
                                stock: stock,
                                precio: productoPrecio
                            };
                        }
                    },
                    error: function() {
                        Swal.fire("Error", "Error al verificar stock", "error");
                    }
                });
            });
            
            // Agregar producto a la tabla
            $('#btn_agregar_producto').click(function() {
                if (!productoActual) {
                    Swal.fire("Error", "No hay producto seleccionado", "error");
                    return;
                }
                
                const productoId = $('#producto_id').val();
                const cantidad = parseFloat($('#producto_cantidad').val()) || 0;
                const precio = parseFloat($('#producto_precio').val()) || 0;
                const stock = parseFloat($('#producto_stock').val()) || 0;
                
                // Validaciones
                if (productosAgregados.has(parseInt(productoId))) {
                    Swal.fire("Advertencia", "Producto ya agregado", "warning");
                    return;
                }
                
                if (cantidad <= 0 || precio <= 0) {
                    Swal.fire("Error", "Valores inválidos", "error");
                    return;
                }
                
                if (cantidad > stock) {
                    Swal.fire("Error", `Stock insuficiente: ${stock.toFixed(4)}`, "error");
                    return;
                }
                
                // Agregar fila
                cont++;
                const stockRestante = stock - cantidad;
                const subtotal = cantidad * precio;
                
                const fila = `
                    <tr id="fila_${cont}" data-producto-id="${productoId}">
                        <td>${cont}</td>
                        <td class="small">
                            <div class="fw-bold">${productoActual.nombre}</div>
                            <small class="text-muted">${productoActual.codigo}</small>
                            <input type="hidden" name="arrayidproducto[]" value="${productoId}">
                        </td>
                        <td>
                            <input type="number" name="arraycantidad[]" 
                                   class="form-control form-control-sm cantidad" 
                                   value="${cantidad.toFixed(4)}" min="0.001" step="0.001" required>
                        </td>
                        <td>
                            <input type="number" name="arrayprecioventa[]" 
                                   class="form-control form-control-sm precio" 
                                   value="${precio.toFixed(2)}" min="0.01" step="0.01" required>
                        </td>
                        <td>
                            <input type="number" name="arraydescuento[]" 
                                   class="form-control form-control-sm descuento" 
                                   value="0.00" min="0" step="0.01">
                        </td>
                        <td class="${stockRestante < 10 ? 'stock-warning' : 'stock-normal'}">
                            ${stockRestante.toFixed(4)}
                        </td>
                        <td class="subtotal fw-bold">
                            ${subtotal.toFixed(2)}
                        </td>
                        <td>
                            <button type="button" class="btn btn-outline-danger btn-sm btn-eliminar" onclick="eliminarFila(${cont}, ${productoId})">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
                
                $('#tabla_detalle tbody').append(fila);
                productosAgregados.add(parseInt(productoId));
                limpiarProducto();
                calcularTotales();
                mostrarBotones();
            });
            
            // Cancelar venta
            $('#btnCancelarVenta').click(function() {
                $('#tabla_detalle tbody').empty();
                cont = 0;
                productosAgregados.clear();
                limpiarProducto();
                $('#producto_info').fadeOut();
                calcularTotales();
                $('#guardar, #cancelar').fadeOut();
            });
            
            // Cambio de almacén
            $('#almacen_id').on('changed.bs.select', function() {
                if ($('#tabla_detalle tbody tr').length > 0) {
                    Swal.fire({
                        title: 'Cambiar Sucursal',
                        text: 'Se limpiarán los productos actuales',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Aceptar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        customClass: {
                            popup: 'swal2-sm'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#tabla_detalle tbody').empty();
                            cont = 0;
                            productosAgregados.clear();
                            limpiarProducto();
                            $('#producto_info').fadeOut();
                            calcularTotales();
                            $('#guardar, #cancelar').fadeOut();
                        } else {
                            $(this).selectpicker('val', $(this).data('last-value') || '');
                        }
                    });
                }
                $(this).data('last-value', $(this).val());
            });
            
            // Eventos para edición en línea
            $(document).on('input', '.cantidad, .precio, .descuento', function() {
                const fila = $(this).closest('tr');
                actualizarFila(fila);
            });
        });
        
        function actualizarFila(fila) {
            const cantidad = parseFloat(fila.find('.cantidad').val()) || 0;
            const precio = parseFloat(fila.find('.precio').val()) || 0;
            const descuento = parseFloat(fila.find('.descuento').val()) || 0;
            
            const subtotal = (cantidad * precio) - descuento;
            fila.find('.subtotal').text(subtotal.toFixed(2));
            
            calcularTotales();
        }
        
        function eliminarFila(filaId, productoId) {
            $(`#fila_${filaId}`).remove();
            productosAgregados.delete(productoId);
            
            // Renumerar
            $('#tabla_detalle tbody tr').each(function(index) {
                $(this).find('td').first().text(index + 1);
            });
            cont = $('#tabla_detalle tbody tr').length;
            
            calcularTotales();
            if (cont === 0) {
                $('#guardar, #cancelar').fadeOut();
            }
        }
        
        function calcularTotales() {
            let total = 0;
            $('.subtotal').each(function() {
                total += parseFloat($(this).text()) || 0;
            });
            $('#total').text('S/ ' + total.toFixed(2));
            $('#inputTotal').val(total.toFixed(2));
        }
        
        function limpiarProducto() {
            $('#producto_id').val('');
            $('#producto_nombre').val('');
            $('#producto_codigo').val('');
            $('#producto_stock').val('');
            $('#producto_precio').val('');
            $('#producto_cantidad').val('1.000');
            productoActual = null;
            $('#producto_select').selectpicker('val', '');
        }
        
        function mostrarBotones() {
            if ($('#tabla_detalle tbody tr').length > 0) {
                $('#guardar, #cancelar').fadeIn();
            }
        }
        
        // Validar formulario
        $('#ventaForm').submit(function(e) {
            if ($('#tabla_detalle tbody tr').length === 0) {
                e.preventDefault();
                Swal.fire("Error", "Agregue al menos un producto", "error");
                return false;
            }
            
            // Validar que no haya stock negativo
            let error = false;
            $('#tabla_detalle tbody tr').each(function() {
                const stockCell = $(this).find('td').eq(5);
                const stockText = stockCell.text();
                const stock = parseFloat(stockText);
                
                if (stock < 0) {
                    error = true;
                    stockCell.addClass('text-danger fw-bold');
                }
            });
            
            if (error) {
                e.preventDefault();
                Swal.fire("Error", "Hay productos con stock insuficiente", "error");
                return false;
            }
            
            return true;
        });
    </script>
@endpush