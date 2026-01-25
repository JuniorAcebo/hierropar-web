@extends('layouts.app')

@section('title', 'Realizar compra')

@push('css')
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/style_Compra_create.css') }}">

@endpush

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Crear Compra</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('compras.index') }}">Compras</a></li>
            <li class="breadcrumb-item active">Crear Compra</li>
        </ol>
    </div>

    <form action="{{ route('compras.store') }}" method="post" id="compraForm">
        @csrf

        <div class="container-lg mt-4">
            <!-- Sección de Datos Generales -->
            <div class="border-section">
                <div class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Datos Generales
                </div>
                <div class="row">
                    <!--Proveedor-->
                    <div class="col-md-4 mb-3">
                        <label for="proveedor_id" class="form-label">Proveedor:</label>
                        <select name="proveedor_id" id="proveedor_id" class="form-control selectpicker show-tick"
                            data-live-search="true" title="Seleccione un proveedor" data-size='5'>
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

                    <!--Sucursal / Almacen-->
                    <div class="col-md-4 mb-3">
                        <label for="almacen_id" class="form-label">Sucursal:</label>
                        <select name="almacen_id" id="almacen_id" class="form-control selectpicker"
                            title="Seleccione sucursal" required>
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

                    <!--Tipo de comprobante-->
                    <div class="col-md-4 mb-3">
                        <label for="comprobante_id" class="form-label">Comprobante:</label>
                        <select name="comprobante_id" id="comprobante_id" class="form-control selectpicker"
                            title="Seleccione tipo">
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

                    <!--Numero de comprobante-->
                    <div class="col-md-3 mb-3">
                        <label for="numero_comprobante" class="form-label">Nro. Comprobante:</label>
                        <input type="text" name="numero_comprobante" id="numero_comprobante" class="form-control" 
                            value="{{ old('numero_comprobante') }}" placeholder="Ej: 00000001" required>
                        @error('numero_comprobante')
                            <small class="text-danger">{{ '*' . $message }}</small>
                        @enderror
                    </div>

                    <!--Fecha-->
                    <div class="col-md-3 mb-3">
                        <label for="fecha" class="form-label">Fecha:</label>
                        <input type="date" name="fecha" id="fecha" class="form-control"
                            value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="fecha_hora" value="{{ now()->toDateTimeString() }}">
                    </div>

                    <!--Costo Transporte-->
                    <div class="col-md-3 mb-3">
                        <label for="costo_transporte" class="form-label">Costo Transporte:</label>
                        <input type="number" step="0.01" name="costo_transporte" id="costo_transporte" class="form-control" 
                            value="{{ old('costo_transporte', 0) }}">
                    </div>

                    <!--Estado de Entrega-->
                    <div class="col-md-3 mb-3">
                        <label for="estado_entrega" class="form-label">Estado Entrega:</label>
                        <select name="estado_entrega" id="estado_entrega" class="form-control selectpicker">
                            <option value="entregado">Entregado (Suma Stock)</option>
                            <option value="por_entregar">Por recibir (Pendiente)</option>
                        </select>
                    </div>

                    <!--Nota-->
                    <div class="col-12 mb-3">
                        <label for="nota_personal" class="form-label">Notas / Observaciones:</label>
                        <textarea name="nota_personal" id="nota_personal" class="form-control" rows="2">{{ old('nota_personal') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="border-section">
                <div class="section-title">
                    <i class="fas fa-box"></i>
                    Detalles de la Compra
                </div>

                <div class="product-search-container">
                    <div class="search-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="producto_search" class="form-control"
                            placeholder="Buscar producto por nombre o código...">
                        <div class="products-dropdown" id="products_dropdown">
                        </div>
                    </div>
                </div>

                <!-- Campos de Detalle -->
                <div class="detail-inputs">
                    <!-- Cantidad -->
                    <div class="input-group-custom">
                        <label for="cantidad" class="form-label">Cantidad:</label>
                        <input type="number" id="cantidad" class="form-control with-icon" min="0.01" step="0.01"
                            value="1.00" placeholder="1.00">
                    </div>

                    <!-- Precio de compra -->
                    <div class="input-group-custom">
                        <label for="precio_compra" class="form-label">Precio Compra:</label>
                        <input type="number" id="precio_compra" class="form-control with-icon" step="0.01"
                            min="0.01" placeholder="0.00">
                    </div>

                    <!-- Precio de venta -->
                    <div class="input-group-custom">
                        <label for="precio_venta" class="form-label">Precio Venta:</label>
                        <input type="number" id="precio_venta" class="form-control with-icon" step="0.01" min="0.01"
                            placeholder="0.00">
                    </div>

                    <!-- Margen de ganancia (visual) -->
                    <div class="input-group-custom">
                        <label for="margen" class="form-label">Margen:</label>
                        <input type="text" id="margen" disabled class="form-control with-icon" placeholder="0%">
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

                <!-- Tabla para el detalle de la compra -->
                <div class="table-responsive">
                    <table id="tabla_detalle" class="table-detalle">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="25%">Producto</th>
                                <th width="12%">Cantidad</th>
                                <th width="14%">P. Compra</th>
                                <th width="14%">P. Venta</th>
                                <th width="10%">Margen</th>
                                <th width="15%">Subtotal</th>
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
                        Cancelar compra
                    </button>
                    <button type="submit" class="btn btn-success" id="guardar" style="display: none;">
                        <i class="fas fa-check-circle"></i>
                        Realizar compra
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal para cancelar la compra -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">
                            <i class="fas fa-exclamation-triangle"></i> Advertencia
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que quieres cancelar la compra? Se perderán todos los datos ingresados.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button id="btnCancelarCompra" type="button" class="btn btn-danger" data-bs-dismiss="modal">
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
            $('#btnCancelarCompra').click(cancelarCompra);
            disableButtons();

            // Calcular margen cuando cambien los precios
            $('#precio_compra, #precio_venta').on('input', calcularMargen);

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

                let cantidad = parseFloat(fila.find(".cantidad").val()) || 0;
                let compra = parseFloat(fila.find(".compra").val()) || 0;
                let venta = parseFloat(fila.find(".venta").val()) || 0;

                // Validar que precio venta sea mayor que compra
                if (venta <= compra && venta > 0) {
                    fila.find(".venta").addClass('is-invalid');
                    return;
                } else {
                    fila.find(".venta").removeClass('is-invalid');
                }

                // Actualizar valores en los inputs hidden
                fila.find('input[name="arraycantidad[]"]').val(cantidad.toFixed(2));
                fila.find('input[name="arraypreciocompra[]"]').val(compra.toFixed(2));
                fila.find('input[name="arrayprecioventa[]"]').val(venta.toFixed(2));

                // Recalcular subtotal
                let nuevoSubtotal = cantidad * compra;
                fila.find(".sub").text(nuevoSubtotal.toFixed(2));
                fila.find('input[name="arraysubtotal[]"]').val(nuevoSubtotal.toFixed(2));

                // Recalcular margen
                let margen = calcularBadgeMargen(compra, venta);
                fila.find(".margen_td").html(margen);
                fila.find('input[name="arraymargen[]"]').val(margen.replace(/[^0-9.]/g, ''));

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

                const item = $(`
                    <div class="product-item ${yaAgregado ? 'opacity-50' : ''}" data-id="${product.id}">
                        <div class="product-info">
                            <div class="product-name">
                                ${product.nombre}
                                ${yaAgregado ? '<small class="text-warning">(Ya agregado)</small>' : ''}
                            </div>
                            <div class="product-code">Código: ${product.codigo}</div>
                        </div>
                        <div class="product-prices">
                            <div class="product-price compra">Compra: $${parseFloat(product.precio_compra).toFixed(2)}</div>
                            <div class="product-price venta">Venta: $${parseFloat(product.precio_venta).toFixed(2)}</div>
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

            const precioCompra = parseFloat(product.precio_compra) || 0;
            const precioVenta = parseFloat(product.precio_venta) || 0;

            $('#precio_compra').val(precioCompra.toFixed(2));
            $('#precio_venta').val(precioVenta.toFixed(2));
            $('#cantidad').val('1.00').focus();

            calcularMargen();

            // Feedback visual
            $('#producto_search').val(`${product.codigo} - ${product.nombre}`);
        }

        function calcularMargen() {
            const precioCompra = parseFloat($('#precio_compra').val()) || 0;
            const precioVenta = parseFloat($('#precio_venta').val()) || 0;

            if (precioCompra > 0 && precioVenta > 0) {
                const margen = ((precioVenta - precioCompra) / precioCompra * 100).toFixed(2);
                $('#margen').val(margen + '%');

                // Cambiar color según el margen
                const margenElement = $('#margen');
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
                Swal.fire("Advertencia", "Seleccione un producto.", "warning");
                return;
            }

            let id = productoSeleccionado.id;

            if (productosAgregados.has(id)) {
                Swal.fire("Advertencia", "El producto ya está agregado.", "warning");
                return;
            }

            let cantidad = parseFloat($('#cantidad').val()) || 0;
            let compra = parseFloat($('#precio_compra').val()) || 0;
            let venta = parseFloat($('#precio_venta').val()) || 0;

            if (cantidad <= 0 || compra <= 0 || venta <= 0) {
                Swal.fire("Error", "Complete los precios y cantidad correctamente.", "error");
                return;
            }

            if (venta <= compra) {
                Swal.fire("Error", "El precio de venta debe ser mayor al precio de compra.", "error");
                return;
            }

            let sub = (cantidad * compra).toFixed(2);
            let margen = calcularBadgeMargen(compra, venta);

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
                               type="number" min="0.01" step="0.01"
                               value="${cantidad.toFixed(2)}">
                        <input type="hidden" name="arraycantidad[]" value="${cantidad.toFixed(2)}">
                    </td>
                    <td>
                        <input class="edit compra form-control form-control-sm"
                               type="number" min="0.01" step="0.01"
                               value="${compra.toFixed(2)}">
                        <input type="hidden" name="arraypreciocompra[]" value="${compra.toFixed(2)}">
                    </td>
                    <td>
                        <input class="edit venta form-control form-control-sm"
                               type="number" min="0.01" step="0.01"
                               value="${venta.toFixed(2)}">
                        <input type="hidden" name="arrayprecioventa[]" value="${venta.toFixed(2)}">
                    </td>
                    <td class="margen_td">
                        ${margen}
                        <input type="hidden" name="arraymargen[]" value="${margen.replace(/[^0-9.]/g, '')}">
                    </td>
                    <td class="sub precio-highlight">
                        ${sub}
                        <input type="hidden" name="arraysubtotal[]" value="${sub}">
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

        function calcularBadgeMargen(compra, venta) {
            let m = ((venta - compra) / compra) * 100;
            let margen = m.toFixed(2);

            if (m < 10) return `<span class="badge-modern margen-bajo">${margen}%</span>`;
            if (m < 30) return `<span class="badge-modern margen-medio">${margen}%</span>`;
            return `<span class="badge-modern margen-alto">${margen}%</span>`;
        }

        function calcularTotales() {
            let sum = 0;

            $(".sub").each(function() {
                sum += parseFloat($(this).text()) || 0;
            });

            $("#subtotal").text(sum.toFixed(2));
            $("#total").text(sum.toFixed(2));
            $("#inputTotal").val(sum.toFixed(2));

            if (sum > 0) {
                $('#guardar, #cancelar').fadeIn();
            } else {
                $('#guardar, #cancelar').fadeOut();
            }
        }

        function cancelarCompra() {
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
            $('#cantidad').val('1.00');
            $('#precio_compra').val('');
            $('#precio_venta').val('');
            $('#margen').val('0%').removeClass('margen-bajo margen-medio margen-alto');
        }
    </script>
@endpush
