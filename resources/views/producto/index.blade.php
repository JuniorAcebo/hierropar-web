@extends('layouts.app')

@section('title', 'Productos')

@push('css-datatable')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/style_Categoria.css') }}">
    
@endpush

@section('content')
    @include('layouts.partials.alert')

    <div class="container-fluid px-4">
        <h1 class="mt-4 text-center" style="color: #2c3e50; font-weight: 700;">Gestión de Productos</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" style="text-decoration: none;">Inicio</a></li>
            <li class="breadcrumb-item active" style="color: #667eea;">Productos</li>
        </ol>

        @can('crear-producto')
            <div class="mb-4">
                <a href="{{ route('productos.create') }}" class="btn btn-primary-modern">
                    <i class="fas fa-plus me-2"></i> Añadir Nuevo Producto
                </a>
            </div>
        @endcan

        <div class="card mb-4">
            <div class="card-header">

                Lista de Productos
            </div>
            <div class="card-body">
                <form action="{{ route('productos.index') }}" method="GET" id="searchForm" class="mb-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="busqueda" class="form-control border-start-0 ps-0" 
                                    placeholder="Buscar por código, nombre..." value="{{ $busqueda ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <label for="per_page" class="me-2 text-muted">Mostrar:</label>
                                <select name="per_page" id="per_page" class="form-select w-auto" onchange="submitForm()">
                                    @foreach([5, 10, 15, 20, 25] as $option)
                                        <option value="{{ $option }}" {{ ($perPage ?? 10) == $option ? 'selected' : '' }}>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5 text-end">
                            <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                                <i class="fas fa-undo me-1"></i> Mostrar Todo
                            </a>
                        </div>
                    </div>
                </form>

                <div id="table-container">
                    <div class="datatable-wrapper">
                        <table id="datatablesSimple" class="modern-table">
                           <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>P. Compra</th>
                                    <th>P. Venta</th>
                                    <th>Marca</th>
                                    <th>Tipo Unidad</th>
                                    <th>Categoría</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($productos as $item)
                                <tr>
                                    <td><span class="fw-bold" style="color: #2c3e50;">{{ $item->codigo }}</span></td>
                                    <td><span class="fw-bold" style="color: #2c3e50;">{{ $item->nombre }}</span></td>
                                    <td><span style="color: #2c3e50;">{{ \Illuminate\Support\Str::limit($item->descripcion, 50, '...') }}</span></td>
                                    <td><span class="precio-modern">Bs {{ number_format($item->precio_compra, 2) }}</span></td>
                                    <td><span class="precio-modern">Bs {{ number_format($item->precio_venta, 2) }}</span></td>
                                    <td><span style="color: #7f8c8d;">{{ $item->marca->nombre }}</span></td>
                                    <td><span style="color: #7f8c8d;">{{ $item->tipounidad->nombre }}</span></td>
                                    <td><span class="categoria-modern">{{ $item->categoria->nombre }}</span></td>

                                    <!-- Stock total -->
                                    <td>
                                        <span class="fw-bold {{ $item->inventarios->sum('stock') <= 10 ? 'text-danger' : 'text-success' }}">
                                            {{ $item->inventarios->sum('stock') }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($item->estado)
                                            <span class="badge-modern badge-activo"><i class="fas fa-check-circle me-1"></i>Activo</span>
                                        @else
                                            <span class="badge-modern badge-eliminado"><i class="fas fa-times-circle me-1"></i>Eliminado</span>
                                        @endif
                                    </td>

                                    <td>
                                        
                                        <!-- Acciones -->
                                        <div class="action-btns-modern">
                                            @can('editar-producto')
                                                <a href="{{ route('productos.edit', $item) }}" class="btn-action btn-edit" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                            @can('ver-producto')
                                                <button class="btn-action btn-view" data-bs-toggle="modal"
                                                    data-bs-target="#verModal-{{ $item->id }}" title="Ver Detalles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @endcan

                                            @can('update-estado')
                                                <a href="#" class="btn-action btn-delete" title="Eliminar/Restaurar">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            @endcan

                                            @can('update-stock')
                                                <button class="btn-action btn-success btn-stock" 
                                                    title="Actualizar Stock"
                                                    onclick="seleccionarAccionStock({{ $item->id }})">
                                                    <i class="fas fa-sync-alt text-white"></i>
                                                </button>
                                            @endcan

                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal de detalles -->
                                <div class="modal fade" id="verModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Detalles del producto</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="row mb-2">
                                                    <div class="col-md-6"><p><span class="fw-bold">Código:</span> {{ $item->codigo }}</p></div>
                                                    <div class="col-md-6"><p><span class="fw-bold">Nombre:</span> {{ $item->nombre }}</p></div>
                                                </div>

                                                <div class="row mb-2">
                                                    <div class="col-md-12"><p><span class="fw-bold">Descripción:</span> {{ $item->descripcion }}</p></div>
                                                </div>

                                                <div class="row mb-2">
                                                    <div class="col-md-6"><p><span class="fw-bold">Precio Compra:</span> Bs {{ number_format($item->precio_compra, 2) }}</p></div>
                                                    <div class="col-md-6"><p><span class="fw-bold">Precio Venta:</span> Bs {{ number_format($item->precio_venta, 2) }}</p></div>
                                                </div>

                                                <div class="row mb-2">
                                                    <div class="col-md-6"><p><span class="fw-bold">Marca:</span> {{ $item->marca->nombre }}</p></div>
                                                    <div class="col-md-6"><p><span class="fw-bold">Tipo Unidad:</span> {{ $item->tipounidad->nombre }}</p></div>
                                                </div>

                                                <div class="row mb-2">
                                                    <div class="col-md-6"><p><span class="fw-bold">Categoría:</span> {{ $item->categoria->nombre }}</p></div>
                                                    <div class="col-md-6">
                                                        <p><span class="fw-bold">Estado:</span>
                                                            @if ($item->estado)
                                                                <span class="badge-modern badge-activo"><i class="fas fa-check-circle me-1"></i>Activo</span>
                                                            @else
                                                                <span class="badge-modern badge-eliminado"><i class="fas fa-times-circle me-1"></i>Eliminado</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="row mb-2">
                                                    <div class="col-md-12">
                                                        <p><span class="fw-bold">Stock por sucursal:</span></p>
                                                        @if($item->inventarios->isNotEmpty())
                                                            <ul>
                                                                @foreach($item->inventarios as $inv)
                                                                    <li>{{ $inv->almacen->nombre }}: {{ $inv->stock }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <p>No tiene inventario registrado</p>
                                                        @endif
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal Aumentar Stock -->
                                <div class="modal fade" id="aumentarStockModal-{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('productos.updateStock', $item) }}" method="POST" id="form-stock-{{ $item->id }}">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Aumentar Stock</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Seleccione los almacenes y la cantidad a sumar:</p>

                                                    @foreach($item->inventarios as $inv)
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input stock-checkbox" type="checkbox" 
                                                                id="chk-{{ $item->id }}-{{ $inv->almacen_id }}" 
                                                                data-target="input-{{ $item->id }}-{{ $inv->almacen_id }}">
                                                            <label class="form-check-label fw-bold" for="chk-{{ $item->id }}-{{ $inv->almacen_id }}">
                                                                {{ $inv->almacen->nombre }} (Stock actual: {{ $inv->stock }})
                                                            </label>
                                                            <input type="number" name="stocks[{{ $inv->almacen_id }}]" 
                                                                class="form-control mt-1" 
                                                                id="input-{{ $item->id }}-{{ $inv->almacen_id }}" 
                                                                placeholder="Cantidad a sumar" 
                                                                value="0" min="0" step="1" disabled>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Actualizar Stock</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Modal Agregar Producto a Almacén Nuevo -->
                                <div class="modal fade" id="agregarAlmacenModal-{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('productos.addAlmacen', $item) }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Agregar a Almacén</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label class="form-label">Seleccione almacén:</label>
                                                    <select name="almacen_id" class="form-select" required>
                                                        @foreach($almacenes->where('estado', true) as $almacen) {{-- Solo activos --}}
                                                            @if(!$item->inventarios->contains('almacen_id', $almacen->id))
                                                                <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>

                                                    <label class="form-label mt-2">Stock inicial:</label>
                                                    <input type="number" name="stock" class="form-control" min="0" value="0" required>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Agregar Producto</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>


                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }} de {{ $productos->total() }} registros
                        </div>
                        <div>
                            {{ $productos->appends(['busqueda' => $busqueda, 'per_page' => $perPage])->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('js')

<script>
let debounceTimer;
const searchInput = document.querySelector('input[name="busqueda"]');
const perPageSelect = document.getElementById('per_page');
const tableContainer = document.getElementById('table-container');

// ========================
// BÚSQUEDA
// ========================
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchProducts, 300);
    });

    searchInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            clearTimeout(debounceTimer);
            fetchProducts();
        }
    });
}

function fetchProducts(url = null) {
    const query = searchInput.value;
    const perPage = perPageSelect.value;
    let fetchUrl = url;

    if (!fetchUrl) {
        fetchUrl = "{{ route('productos.index') }}";
        const params = new URLSearchParams();
        if (query) params.append('busqueda', query);
        if (perPage) params.append('per_page', perPage);
        fetchUrl += `?${params.toString()}`;
    }

    tableContainer.style.opacity = '0.5';

    fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            tableContainer.innerHTML = parser.parseFromString(html, 'text/html')
                                         .getElementById('table-container').innerHTML;
            tableContainer.style.opacity = '1';
            attachPaginationListeners();
            attachStockCheckboxListeners();
        })
        .catch(error => {
            console.error('Error:', error);
            tableContainer.style.opacity = '1';
        });
}

function submitForm() {
    fetchProducts();
}

// ========================
// PAGINACIÓN
// ========================
function attachPaginationListeners() {
    document.querySelectorAll('.pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            fetchProducts(this.href);
        });
    });
}

// ========================
// MODALES DE STOCK
// ========================
function seleccionarAccionStock(productId) {
    Swal.fire({
        title: 'Seleccione una acción',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: 'Aumentar stock',
        denyButtonText: 'Agregar a almacén',
        cancelButtonText: 'Cancelar',
        icon: 'question'
    }).then(result => {
        if (result.isConfirmed) {
            new bootstrap.Modal(document.getElementById('aumentarStockModal-' + productId)).show();
        } else if (result.isDenied) {
            new bootstrap.Modal(document.getElementById('agregarAlmacenModal-' + productId)).show();
        }
    });
}

// Habilitar/deshabilitar input según checkbox
function attachStockCheckboxListeners() {
    document.querySelectorAll('.stock-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const input = document.getElementById(this.dataset.target);
            if (this.checked) {
                input.disabled = false;
                if (parseFloat(input.value) === 0) input.value = '';
                input.focus();
            } else {
                input.disabled = true;
                input.value = 0;
            }
        });
    });
}

document.addEventListener('submit', function(e) {
    if (!e.target.id.startsWith('form-stock-')) return;

    e.preventDefault(); // Prevenir envío

    const form = e.target;
    const checkboxes = form.querySelectorAll('.stock-checkbox');

    let anyChecked = false;
    let hasValidQuantity = false;

    checkboxes.forEach(chk => {
        const input = document.getElementById(chk.dataset.target);

        if (chk.checked) {
            anyChecked = true;
            const cantidad = parseFloat(input.value) || 0;
            if (cantidad > 0) hasValidQuantity = true;

            input.disabled = false; // asegurar que se envíe al servidor
        } else {
            input.disabled = true;      // evitar que se envíe
            input.removeAttribute('name'); // clave: quitar name evita envío
        }
    });

    if (!anyChecked) {
        Swal.fire('Error', 'Debe seleccionar al menos un almacén', 'warning');
        return;
    }

    if (!hasValidQuantity) {
        Swal.fire('Error', 'Debe ingresar al menos una cantidad mayor a 0', 'warning');
        return;
    }

    form.submit(); // ahora solo se envían los inputs de almacenes seleccionados
});



// ========================
// Inicialización
// ========================
document.addEventListener('DOMContentLoaded', attachStockCheckboxListeners);
</script>


    @endpush
