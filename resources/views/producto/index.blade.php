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
                                            <span class="badge-modern badge-eliminado"><i class="fas fa-times-circle me-1"></i>Inactivo</span>
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
                                            <form action="{{ route('productos.updateEstado', $item) }}"
                                                method="POST"
                                                class="d-inline form-update-estado">
                                                @csrf
                                                @method('PATCH')

                                                <button type="button"
                                                    class="btn-action {{ $item->estado ? 'btn-delete' : 'btn-restore' }} btn-toggle-estado"
                                                    data-nombre="{{ $item->nombre }}"
                                                    data-estado="{{ $item->estado ? 'desactivar' : 'activar' }}"
                                                    title="{{ $item->estado ? 'Desactivar' : 'Activar' }}">
                                                    <i class="fas {{ $item->estado ? 'fa-trash' : 'fa-check' }}"></i>
                                                </button>
                                            </form>
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
                                                                <span class="badge-modern badge-eliminado"><i class="fas fa-times-circle me-1"></i>Inactivo</span>
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

document.addEventListener('click', function (e) {
    if (e.target.closest('.btn-toggle-estado')) {
        const btn = e.target.closest('.btn-toggle-estado');
        const form = btn.closest('form');

        const nombre = btn.dataset.nombre;
        const accion = btn.dataset.estado;

        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas ${accion} el producto "${nombre}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
});

</script>


    @endpush
