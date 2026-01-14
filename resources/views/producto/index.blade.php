@extends('layouts.app')

@section('title', 'Productos')

@push('css-datatable')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 0.5rem;
            font-size: 1.25rem;
            font-weight: 600;
            border-bottom: none;
            position: relative;
        }

        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.5), transparent);
        }

        .card-body {
            background-color: #fafbfc;
            padding: 2rem;
        }

        /* TABLA MEJORADA - DISEÑO MODERNO */
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            font-size: 0.95em;
            margin: 0;
        }

        .modern-table thead {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        }

        .modern-table th {
            color: #ecf0f1;
            padding: 1.2rem 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
            position: relative;
        }

        .modern-table th::after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 1px;
            height: 60%;
            background: rgba(255, 255, 255, 0.2);
        }

        .modern-table th:last-child::after {
            display: none;
        }

        .modern-table td {
            padding: 1.2rem 1rem;
            text-align: left;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f4;
            background: #fff;
            transition: all 0.3s ease;
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
        }

        .modern-table tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .modern-table tbody tr:hover td {
            background-color: #f8f9fa;
        }

        /* BADGES MEJORADOS */
        .badge-modern {
            font-size: 0.8rem;
            padding: 0.5em 1em;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            text-align: center;
            min-width: 85px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .badge-activo {
            background: linear-gradient(135deg, #00b09b, #96c93d);
            color: white;
        }

        .badge-eliminado {
            background: linear-gradient(135deg, #ff416c, #ff4b2b);
            color: white;
        }

        /* BADGES PARA CATEGORÍAS MEJORADOS */
        .categoria-modern {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 0.4em 0.8em;
            font-size: 0.75em;
            font-weight: 500;
            border-radius: 15px;
            margin: 2px;
            white-space: nowrap;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .categoria-modern:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* BOTONES DE ACCIÓN MEJORADOS - DISEÑO COHESIVO */
        .action-btns-modern {
            display: flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: none;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .btn-action::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-action:hover::before {
            left: 100%;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .btn-action:active {
            transform: translateY(0);
        }

        .btn-edit {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }

        .btn-view {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
        }

        .btn-delete {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .btn-restore {
            background: linear-gradient(135deg, #27ae60, #229954);
        }

        .btn-action i {
            font-size: 0.9em;
            z-index: 1;
        }

        /* BOTÓN PRIMARIO MEJORADO */
        .btn-primary-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        /* ESTILOS PARA PRECIOS */
        .precio-modern {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #2c3e50;
            background: #f8f9fa;
            padding: 0.3em 0.6em;
            border-radius: 6px;
            border-left: 3px solid #3498db;
        }

        /* MEJORAS EN EL BUSCADOR DATATABLES */
        .datatable-search input {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            background: #fff;
        }

        .datatable-search input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        /* RESPONSIVE MEJORADO */
        @media screen and (max-width: 1200px) {
            .modern-table {
                display: block;
                overflow-x: auto;
            }

            .categoria-modern {
                display: block;
                margin-bottom: 4px;
                text-align: center;
            }
        }

        @media screen and (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .modern-table th,
            .modern-table td {
                padding: 1rem 0.75rem;
                font-size: 0.85em;
            }

            .action-btns-modern {
                flex-wrap: wrap;
                gap: 5px;
            }

            .btn-action {
                width: 34px;
                height: 34px;
            }

            .badge-modern {
                min-width: 70px;
                font-size: 0.75em;
            }
        }

        /* ANIMACIONES SUAVES */
        .modern-table tbody tr {
            animation: fadeInUp 0.5s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ESTADO DE CARGA */
        .loading-state {
            text-align: center;
            padding: 2rem;
            color: #6c757d;
        }

        .loading-state i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #667eea;
        }
    </style>
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
                                    <th>Marca</th>
                                    <th>Presentación</th>
                                    <th>Categorías</th>
                                    <th>P. Compra</th>
                                    <th>P. Venta</th>
                                    <th>Stock</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productos as $item)
                                    <tr>
                                        <td>
                                            <span class="fw-bold" style="color: #2c3e50;">{{ $item->codigo }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold" style="color: #2c3e50;">{{ $item->nombre }}</span>
                                        </td>
                                        <td>
                                            <span style="color: #7f8c8d;">{{ $item->marca->caracteristica->nombre }}</span>
                                        </td>
                                        <td>
                                            <span
                                                style="color: #7f8c8d;">{{ $item->presentacione->caracteristica->nombre }}</span>
                                        </td>
                                        <td>
                                            @foreach ($item->categorias as $category)
                                                <span class="categoria-modern">{{ $category->caracteristica->nombre }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <span class="precio-modern">Bs {{ number_format($item->precio_compra, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="precio-modern">Bs {{ number_format($item->precio_venta, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold {{ $item->stock <= 10 ? 'text-danger' : 'text-success' }}">
                                                {{ $item->stock }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($item->estado == 1)
                                                <span class="badge-modern badge-activo">
                                                    <i class="fas fa-check-circle me-1"></i>Activo
                                                </span>
                                            @else
                                                <span class="badge-modern badge-eliminado">
                                                    <i class="fas fa-times-circle me-1"></i>Eliminado
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-btns-modern">
                                                <!-- Botón Editar -->
                                                @can('editar-producto')
                                                    <a href="{{ route('productos.edit', $item) }}" class="btn-action btn-edit"
                                                        title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan

                                                <!-- Botón Ver -->
                                                @can('ver-producto')
                                                    <button class="btn-action btn-view" data-bs-toggle="modal"
                                                        data-bs-target="#verModal-{{ $item->id }}" title="Ver Detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endcan

                                                <!-- Botón Eliminar/Restaurar -->
                                                @can('eliminar-producto')
                                                    <button class="btn-action {{ $item->estado ? 'btn-delete' : 'btn-restore' }}"
                                                        data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $item->id }}"
                                                        title="{{ $item->estado ? 'Eliminar' : 'Restaurar' }}">
                                                        <i class="{{ $item->estado ? 'fas fa-trash' : 'fas fa-rotate-left' }}"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Modal de detalles -->
                                    <div class="modal fade" id="verModal-{{ $item->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Detalles del producto</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <p><span class="fw-bold">Código:</span> {{ $item->codigo }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><span class="fw-bold">Nombre:</span> {{ $item->nombre }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-12">
                                                            <p><span class="fw-bold">Descripción:</span>
                                                                {{ $item->descripcion ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <p><span class="fw-bold">Fecha vencimiento:</span>
                                                                {{ $item->fecha_vencimiento ?? 'No tiene' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><span class="fw-bold">Stock:</span> {{ $item->stock }}</p>
                                                        </div>
                                                    </div>

                                                    @if ($item->img_path != null)
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <p class="fw-bold">Imagen:</p>
                                                                <img src="{{ asset('storage/productos/' . $item->img_path) }}"
                                                                    alt="{{ $item->nombre }}" class="img-fluid rounded"
                                                                    style="max-height: 200px;"
                                                                    onerror="this.onerror=null; this.src='{{ asset('images/default-product.png') }}';">
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <p class="fw-bold">Imagen:</p>
                                                                <img src="{{ asset('images/default-product.png') }}"
                                                                    alt="Imagen no disponible" class="img-fluid rounded"
                                                                    style="max-height: 200px; opacity: 0.5;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cerrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal de confirmación -->
                                    <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmación</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ $item->estado == 1 ? '¿Seguro que quieres eliminar este producto?' : '¿Seguro que quieres restaurar este producto?' }}
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('productos.destroy', ['producto' => $item->id]) }}"
                                                        method="post">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger">Confirmar</button>
                                                    </form>
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

            // Event Listeners
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        fetchProducts();
                    }, 300); // 300ms delay
                });

                // Prevent form submission on enter
                searchInput.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        clearTimeout(debounceTimer);
                        fetchProducts();
                    }
                });
            }

            // Function to fetch products via AJAX
            function fetchProducts(url = null) {
                const query = searchInput.value;
                const perPage = perPageSelect.value;
                
                // Construct URL
                let fetchUrl = url;
                if (!fetchUrl) {
                    fetchUrl = "{{ route('productos.index') }}";
                    const params = new URLSearchParams();
                    if (query) params.append('busqueda', query);
                    if (perPage) params.append('per_page', perPage);
                    fetchUrl += `?${params.toString()}`;
                }

                // Add loading state
                tableContainer.style.opacity = '0.5';

                fetch(fetchUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // Parse the returned HTML to find the table container
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTableContent = doc.getElementById('table-container').innerHTML;
                    
                    tableContainer.innerHTML = newTableContent;
                    tableContainer.style.opacity = '1';
                    attachPaginationListeners(); // Re-attach listeners to new links
                })
                .catch(error => {
                    console.error('Error:', error);
                    tableContainer.style.opacity = '1';
                });
            }

            // Replace the original submitForm function
            function submitForm() {
                fetchProducts();
            }

            // Handle Pagination Clicks
            function attachPaginationListeners() {
                const paginationLinks = document.querySelectorAll('.pagination a');
                paginationLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        fetchProducts(this.href);
                    });
                });
            }

            // Initial attachment
            document.addEventListener('DOMContentLoaded', function() {
                attachPaginationListeners();
            });
        </script>
    @endpush
