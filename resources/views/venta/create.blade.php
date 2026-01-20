@extends('layouts.app')

@section('title', 'Realizar venta')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/style_Venta_create.css') }}">
@endpush

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Realizar Venta</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
            <li class="breadcrumb-item active">Realizar Venta</li>
        </ol>
    </div>

    <form action="{{ route('ventas.store') }}" method="post" id="ventaForm">
        @csrf
        <div class="container-lg mt-4">
            <!-- Sección de Datos Generales -->
            <div class="border-section">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Datos Generales
                </div>
                <div class="row">
                    <!--Cliente-->
                    <div class="col-md-6 mb-3">
                        <label for="cliente_id" class="form-label">Cliente:</label>
                        <select name="cliente_id" id="cliente_id" class="form-control selectpicker show-tick"
                            data-live-search="true" title="Seleccione un cliente" data-size='5'>
                            @foreach ($clientes as $item)
                                <option value="{{ $item->id }}">{{ $item->persona->razon_social }}</option>
                            @endforeach
                        </select>
                        @error('cliente_id')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                    </div>

                    <!--Tipo de comprobante-->
                    <div class="col-md-6 mb-3">
                        <label for="comprobante_id" class="form-label">Comprobante:</label>
                        <select name="comprobante_id" id="comprobante_id" class="form-control selectpicker"
                            title="Seleccione tipo">
                            @foreach ($comprobantes as $item)
                                <option value="{{ $item->id }}">{{ $item->tipo_comprobante }}</option>
                            @endforeach
                        </select>
                        @error('comprobante_id')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                    </div>

                    <!--Numero de comprobante (oculto y generado automáticamente)-->
                    <input type="hidden" name="numero_comprobante" value="{{ $nextComprobanteNumber }}">

                    <!--Fecha-->
                    <div class="col-md-6 mb-3">
                        <label for="fecha" class="form-label">Fecha:</label>
                        <input readonly type="date" name="fecha" id="fecha" class="form-control"
                            value="{{ now()->format('Y-m-d') }}">
                        <input type="hidden" name="fecha_hora" value="{{ now()->toDateTimeString() }}">
                    </div>

                    <!--User-->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vendedor:</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->name }}" disabled>
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    </div>
                </div>
            </div>

            <!-- Sección de Detalles de Venta -->
            <div class="border-section">
                <div class="section-title">
                    <i class="fas fa-shopping-cart"></i>
                    Detalles de la Venta
                </div>

                <!-- Buscador de Productos Mejorado -->
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
                        <input disabled id="stock" type="text" class="form-control with-icon" placeholder="0.0000">
                    </div>

                    <!-- Precio de venta -->
                    <div class="input-group-custom">
                        <label for="precio_venta" class="form-label">Precio Venta:</label>
                        <input type="number" id="precio_venta"
                               class="form-control with-icon" step="0.01" min="0.01"
                               placeholder="0.00">
                        <input type="hidden" id="precio_compra">
                    </div>

                    <!-- Cantidad -->
                    <div class="input-group-custom">
                        <label for="cantidad" class="form-label">Cantidad:</label>
                        <input type="number" id="cantidad"
                               class="form-control with-icon" min="0.001" step="0.001"
                               value="1.000" placeholder="1.000">
                    </div>

                    <!-- Descuento -->
                    <div class="input-group-custom">
                        <label for="descuento" class="form-label">Descuento:</label>
                        <input type="number" id="descuento"
                               class="form-control with-icon" min="0" step="0.01"
                               value="0.00" placeholder="0.00">
                    </div>

                    <!-- Botón Agregar -->
                    <div>
                        <label class="form-label" style="visibility: hidden;">Acción</label>
                        <button id="btn_agregar" class="btn btn-primary w-100" type="button">
                            <i class="fas fa-plus-circle"></i>
                            Agregar
                        </button>
                    </div>
                </div>

                <!-- Tabla para el detalle de la venta -->
                <div class="table-responsive">
                    <table id="tabla_detalle" class="table-detalle">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="25%">Producto</th>
                                <th width="10%">Cantidad</th>
                                <th width="12%">P. Venta</th>
                                <th width="12%">Descuento</th>
                                <th width="10%">Stock Restante</th>
                                <th width="16%">Subtotal</th>
                                <th width="5%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Contenido dinámico -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-end">Subtotal</th>
                                <th colspan="2"><span id="subtotal" class="precio-highlight">0.00</span></th>
                            </tr>
                            <tr>
                                <th colspan="6" class="text-end">Total</th>
                                <th colspan="2">
                                    <input type="hidden" name="total" value="0" id="inputTotal">
                                    <span id="total" class="precio-highlight">0.00</span>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-between mt-4">
                    <button id="cancelar" type="button" class="btn btn-danger" data-bs-toggle="modal"
                        data-bs-target="#exampleModal" style="display: none;">
                        <i class="fas fa-times-circle"></i>
                        Cancelar venta
                    </button>
                    <button type="submit" class="btn btn-success" id="guardar" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                        Realizar venta
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal para cancelar la venta -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                            <i class="fas fa-exclamation-triangle"></i> Advertencia
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que quieres cancelar la venta? Se perderán todos los datos ingresados.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button id="btnCancelarVenta" type="button" class="btn btn-danger" data-bs-dismiss="modal">
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
        // Datos de productos
        const productos = @json($productos);
        let productoSeleccionado = null;

        // Variables globales
        let cont = 0;
        let subtotal = 0;
        let total = 0;
        let productosAgregados = new Set();

        $(document).ready(function() {
            $('#btn_agregar').click(agregarProducto);
            $('#btnCancelarVenta').click(cancelarVenta);
            disableButtons();

            // Buscador de productos mejorado
            const searchInput = $('#producto_search');
            const dropdown = $('#products_dropdown');

            searchInput.on('input', function() {
                const searchTerm = $(this).val().toLowerCase().trim();

                if (searchTerm.length === 0) {
                    dropdown.removeClass('show').empty();
                    return;
                }

                const filteredProducts = productos.filter(p => {
                    const nombre = p.nombre.toLowerCase();
                    const codigo = p.codigo.toLowerCase();
                    return nombre.includes(searchTerm) || codigo.includes(searchTerm);
                });

                renderDropdown(filteredProducts);
            });

            // Cerrar dropdown al hacer click fuera
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.product-search-container').length) {
                    dropdown.removeClass('show');
                }
            });

            // Eventos para edición en línea
            $(document).on("input", ".edit", function() {
                let fila = $(this).closest("tr");
                let id = fila.data('id');
                let producto = productos.find(p => p.id == id);

                let cantidad = parseFloat(fila.find(".cantidad").val()) || 0;
                let precioVenta = parseFloat(fila.find(".venta").val()) || 0;
                let descuento = parseFloat(fila.find(".descuento").val()) || 0;

                // Validar stock
                if (cantidad > producto.stock) {
                    fila.find(".cantidad").addClass('is-invalid');
                    return;
                } else {
                    fila.find(".cantidad").removeClass('is-invalid');
                }

                // Validar precio
                if (precioVenta <= 0) {
                    fila.find(".venta").addClass('is-invalid');
                    return;
                } else {
                    fila.find(".venta").removeClass('is-invalid');
                }

                // Actualizar valores en los inputs hidden
                fila.find('input[name="arraycantidad[]"]').val(cantidad.toFixed(4));
                fila.find('input[name="arrayprecioventa[]"]').val(precioVenta.toFixed(2));
                fila.find('input[name="arraydescuento[]"]').val(descuento.toFixed(2));

                // Recalcular subtotal
                let nuevoSubtotal = (cantidad * precioVenta) - descuento;
                fila.find(".sub").text(nuevoSubtotal.toFixed(2));
                fila.find('input[name="arraysubtotal[]"]').val(nuevoSubtotal.toFixed(2));

                // Actualizar stock restante
                let stockRestante = producto.stock - cantidad;
                fila.find(".stock-restante").text(stockRestante.toFixed(4));
                fila.find('input[name="arraystockrestante[]"]').val(stockRestante.toFixed(4));

                // Actualizar clase de stock
                fila.find(".stock-restante").removeClass('stock-warning precio-highlight');
                if (stockRestante < 10) {
                    fila.find(".stock-restante").addClass('stock-warning');
                } else {
                    fila.find(".stock-restante").addClass('precio-highlight');
                }

                calcularTotales();
            });

            // Eliminar producto
            $(document).on("click", ".eliminar", function() {
                let fila = $(this).closest("tr");
                let id = parseInt(fila.data("id"));

                productosAgregados.delete(id);
                fila.remove();

                // Renumerar filas
                $('#tabla_detalle tbody tr').each(function(index) {
                    $(this).find('td').first().text(index + 1);
                });

                calcularTotales();
            });
        });

        function renderDropdown(products) {
            const dropdown = $('#products_dropdown');
            dropdown.empty();

            if (products.length === 0) {
                dropdown.html(`
                    <div class="no-results">
                        <i class="fas fa-inbox"></i>
                        <p>No se encontraron productos</p>
                    </div>
                `);
                dropdown.addClass('show');
                return;
            }

            products.forEach(product => {
                const yaAgregado = productosAgregados.has(product.id);
                const stockClass = product.stock <= 0 ? 'stock-out' :
                                  product.stock < 10 ? 'stock-low' : 'stock-normal';

                const item = $(`
                    <div class="product-item ${yaAgregado ? 'opacity-50' : ''}" data-id="${product.id}">
                        <div class="product-info">
                            <div class="product-name">
                                ${product.nombre}
                                ${yaAgregado ? '<small class="text-warning">(Ya agregado)</small>' : ''}
                            </div>
                            <div class="product-code">Código: ${product.codigo}</div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="product-prices">
                                <div class="product-price venta">$${parseFloat(product.precio_venta).toFixed(2)}</div>
                            </div>
                            <span class="product-stock ${stockClass}">
                                ${parseFloat(product.stock).toFixed(4)}
                            </span>
                        </div>
                    </div>
                `);

                if (!yaAgregado && product.stock > 0) {
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

            const stock = parseFloat(product.stock) || 0;
            const precioVenta = parseFloat(product.precio_venta) || 0;
            const precioCompra = parseFloat(product.precio_compra) || 0;

            $('#stock').val(stock.toFixed(4));
            $('#precio_venta').val(precioVenta.toFixed(2));
            $('#precio_compra').val(precioCompra.toFixed(2));
            $('#cantidad').val('1.000').focus();

            // Feedback visual
            $('#producto_search').val(`${product.codigo} - ${product.nombre}`);
        }

        function agregarProducto() {
            if (!productoSeleccionado) {
                Swal.fire("Advertencia", "Seleccione un producto.", "warning");
                return;
            }

            let id = productoSeleccionado.id;

            if (productosAgregados.has(id)) {
                Swal.fire("Advertencia", "El producto ya está agregado.", "warning");
                return;
            }

            let cantidad = parseFloat($('#cantidad').val()) || 0;
            let precioVenta = parseFloat($('#precio_venta').val()) || 0;
            let descuento = parseFloat($('#descuento').val()) || 0;
            let stock = parseFloat(productoSeleccionado.stock);

            // Validaciones
            if (isNaN(cantidad) || cantidad <= 0) {
                Swal.fire("Error", "La cantidad debe ser mayor a 0", "error");
                return;
            }

            if (isNaN(precioVenta) || precioVenta <= 0) {
                Swal.fire("Error", "El precio de venta debe ser mayor a 0", "error");
                return;
            }

            if (isNaN(descuento) || descuento < 0) {
                Swal.fire("Error", "El descuento no puede ser negativo", "error");
                return;
            }

            if (cantidad > stock) {
                Swal.fire("Error", "La cantidad no puede ser mayor al stock disponible", "error");
                return;
            }

            // Calcular valores
            let sub = (cantidad * precioVenta) - descuento;
            let stockRestante = stock - cantidad;
            let stockClass = stockRestante < 10 ? 'stock-warning' : 'precio-highlight';

            cont++;

            let fila = `
                <tr id="fila${cont}" data-id="${id}">
                    <td>${cont}</td>
                    <td class="text-start">
                        <input type="hidden" name="arrayidproducto[]" value="${id}">
                        <div class="fw-bold">${productoSeleccionado.nombre}</div>
                        <small class="text-muted">Código: ${productoSeleccionado.codigo}</small>
                    </td>
                    <td>
                        <input class="edit cantidad form-control form-control-sm"
                               type="number" min="0.001" step="0.001"
                               value="${cantidad.toFixed(4)}">
                        <input type="hidden" name="arraycantidad[]" value="${cantidad.toFixed(4)}">
                    </td>
                    <td>
                        <input class="edit venta form-control form-control-sm"
                               type="number" min="0.01" step="0.01"
                               value="${precioVenta.toFixed(2)}">
                        <input type="hidden" name="arrayprecioventa[]" value="${precioVenta.toFixed(2)}">
                    </td>
                    <td>
                        <input class="edit descuento form-control form-control-sm"
                               type="number" min="0" step="0.01"
                               value="${descuento.toFixed(2)}">
                        <input type="hidden" name="arraydescuento[]" value="${descuento.toFixed(2)}">
                    </td>
                    <td class="stock-restante ${stockClass}">
                        ${stockRestante.toFixed(4)}
                        <input type="hidden" name="arraystockrestante[]" value="${stockRestante.toFixed(4)}">
                    </td>
                    <td class="sub precio-highlight">
                        ${sub.toFixed(2)}
                        <input type="hidden" name="arraysubtotal[]" value="${sub.toFixed(2)}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#tabla_detalle tbody').append(fila);
            productosAgregados.add(id);

            calcularTotales();
            limpiarCampos();
            productoSeleccionado = null;
        }

        function calcularTotales() {
            let sum = 0;

            $(".sub").each(function() {
                sum += parseFloat($(this).text()) || 0;
            });

            $("#subtotal").text(sum.toFixed(2));
            $("#total").text(sum.toFixed(2));
            $("#inputTotal").val(sum.toFixed(2));

            if (sum > 0)
            $('#guardar, #cancelar').fadeIn();
             else
             $('#guardar, #cancelar').fadeOut();
        }

        function cancelarVenta() {
            $('#tabla_detalle tbody').empty();
            cont = 0;
            productosAgregados.clear();
            calcularTotales();
            limpiarCampos();
        }

        function disableButtons() {
            $('#guardar, #cancelar').fadeOut();
        }

        function limpiarCampos() {
            productoSeleccionado = null;
            $('#producto_search').val('');
            $('#stock').val('');
            $('#precio_venta').val('');
            $('#precio_compra').val('');
            $('#cantidad').val('1.000');
            $('#descuento').val('0.00');
        }
    </script>
@endpush
