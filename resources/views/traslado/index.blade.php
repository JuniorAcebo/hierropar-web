@extends('layouts.app')

@section('title', 'Traslados')

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
    <h1 class="mt-4 text-center" style="color: #2c3e50; font-weight: 700;">Gestión de Traslados</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}" style="text-decoration: none;">Inicio</a></li>
        <li class="breadcrumb-item active" style="color: #667eea;">Traslados</li>
    </ol>

    @can('crear-traslado')
    <div class="mb-4">
        <a href="{{ route('traslados.create') }}" class="btn btn-primary-modern">
            <i class="fas fa-plus me-2"></i> Nuevo Traslado
        </a>
        <a href="{{ route('traslados.exportar') }}" class="btn btn-info">
            <i class="fas fa-download me-2"></i> Exportar
        </a>
    </div>
    @endcan

    <div class="card mb-4">
        <div class="card-header">Lista de Traslados</div>
        <div class="card-body">
            <form action="{{ route('traslados.index') }}" method="GET" id="searchForm" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" name="busqueda" class="form-control border-start-0 ps-0" 
                                placeholder="Buscar por origen, destino, usuario..." value="{{ $busqueda ?? '' }}">
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
                        <a href="{{ route('traslados.index') }}" class="btn btn-secondary">
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
                                <th>#</th>
                                <th>Fecha Registro</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Usuario</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($traslados as $traslado)
                            <tr>
                                <td>{{ $traslado->id }}</td>
                                <td>{{ $traslado->fecha_hora->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $traslado->origenAlmacen?->nombre ?? 'N/A' }}</td>
                                <td>{{ $traslado->destinoAlmacen?->nombre ?? 'N/A' }}</td>
                                <td>{{ $traslado->user?->name ?? 'N/A' }}</td>
                                <td>
                                    @if($traslado->estado == 1)
                                        <form action="{{ route('traslados.toggleEstado', $traslado) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <select name="estado" onchange="this.form.submit()" class="form-select form-select-sm" title="Cambiar estado">
                                                <option value="1" {{ $traslado->estado == 1 ? 'selected' : '' }}>Pendiente</option>
                                                <option value="2" {{ $traslado->estado == 2 ? 'selected' : '' }}>Completado</option>
                                                <option value="3" {{ $traslado->estado == 3 ? 'selected' : '' }}>Cancelado</option>
                                            </select>
                                        </form>
                                    @elseif($traslado->estado == 2)
                                        <span class="badge bg-success">Completado</span>
                                    @else
                                        <span class="badge bg-danger">Cancelado</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="action-btns-modern">
                                        @can('ver-traslado')
                                        <button class="btn-action btn-view" data-bs-toggle="modal"
                                            data-bs-target="#verModal-{{ $traslado->id }}" title="Ver Detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @endcan

                                        @can('editar-traslado')
                                                <a href="{{ route('traslados.edit', $traslado) }}" class="btn-action btn-edit" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan

                                        @can('eliminar-traslado')
                                            <form action="{{ route('traslados.destroy', $traslado) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="accion" value="eliminar">
                                                <button type="button" class="btn-action btn-delete btn-real-delete"
                                                    data-nombre="Traslado #{{ $traslado->id }}" title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endcan 
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Mostrando {{ $traslados->firstItem() }} a {{ $traslados->lastItem() }} de {{ $traslados->total() }} registros
                    </div>
                    <div>
                        {{ $traslados->appends(['busqueda' => $busqueda, 'per_page' => $perPage])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALES FUERA DE LA TABLA -->
@foreach($traslados as $traslado)
<div class="modal fade" id="verModal-{{ $traslado->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detalles del Traslado #{{ $traslado->id }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- INFORMACIÓN GENERAL -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="p-2 border rounded bg-light">
                            <strong>Fecha Registro:</strong> <br>{{ $traslado->fecha_hora->format('d/m/Y H:i:s') }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 border rounded bg-light">
                            <strong>Usuario:</strong> <br>{{ $traslado->user?->name ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 border rounded bg-light">
                            <strong>Almacén Origen:</strong> <br>{{ $traslado->origenAlmacen?->nombre ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 border rounded bg-light">
                            <strong>Almacén Destino:</strong> <br>{{ $traslado->destinoAlmacen?->nombre ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 border rounded bg-light">
                            <strong>Costo de Envío:</strong> <br>Bs {{ number_format($traslado->costo_envio, 2) }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        @php
                            $estadoColor = match($traslado->estado) {
                                1 => 'warning',
                                2 => 'success',
                                3 => 'danger',
                                default => 'secondary',
                            };
                            $estadoText = match($traslado->estado) {
                                1 => 'Pendiente',
                                2 => 'Completado',
                                3 => 'Cancelado',
                                default => 'Desconocido',
                            };
                        @endphp
                        <div class="p-2 border rounded bg-{{ $estadoColor }} text-white text-center fw-bold">
                            {{ $estadoText }}
                        </div>
                    </div>
                </div>

                <!-- PRODUCTOS -->
                <h6 class="fw-bold mt-3 mb-2 border-bottom pb-1">Productos Trasladados</h6>
                @if($traslado->detalles->count())
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($traslado->detalles as $detalle)
                                <tr>
                                    <td>{{ $detalle->producto?->nombre ?? 'Producto eliminado' }}</td>
                                    <td class="text-center">{{ $detalle->cantidad }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-warning py-2 mb-0 text-center">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No hay productos asociados a este traslado.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endforeach


@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script>
let debounceTimer;
const searchInput = document.querySelector('input[name="busqueda"]');
const perPageSelect = document.getElementById('per_page');
const tableContainer = document.getElementById('table-container');

if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(fetchTraslados, 300);
    });
    searchInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            clearTimeout(debounceTimer);
            fetchTraslados();
        }
    });
}

function fetchTraslados(url = null) {
    const query = searchInput.value;
    const perPage = perPageSelect.value;
    let fetchUrl = url;

    if (!fetchUrl) {
        fetchUrl = "{{ route('traslados.index') }}";
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
        })
        .catch(error => {
            console.error('Error:', error);
            tableContainer.style.opacity = '1';
        });
}

function submitForm() {
    fetchTraslados();
}

document.addEventListener('click', function (e) {
    if (e.target.closest('.btn-real-delete')) {
        const btn = e.target.closest('.btn-real-delete');
        const form = btn.closest('form');
        const nombre = btn.dataset.nombre;

        Swal.fire({
            title: '¿Estás seguro?',
            text: `Deseas eliminar "${nombre}"`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
});

// Mostrar SweetAlert si hay un mensaje de error
@if ($errors->any() || session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error',
        html: `
            @if (session('error'))
                {{ session('error') }}
            @endif
            @if ($errors->any())
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        `,
        confirmButtonText: 'Aceptar'
    });
@endif

</script>
@endpush
