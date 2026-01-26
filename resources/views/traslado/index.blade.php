@extends('layouts.app')

@section('title', 'Traslados')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .card { border: none; border-radius: 10px; box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08); }
    .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; font-weight: 700; font-size: 1.2rem; border: none; }
    .card-body { background-color: #fafbfc; padding: 1rem; }
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0; background-color: #fff; border-radius: 8px; overflow: hidden; font-size: 0.95em; }
    .modern-table thead { background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: #ecf0f1; }
    .modern-table th, .modern-table td { padding: 0.8rem; text-align: left; vertical-align: middle; }
    .modern-table tbody tr:hover { background-color: #f8f9fa; transform: translateY(-1px); }
    .action-btns-modern { display: flex; gap: 5px; justify-content: center; flex-wrap: wrap; }
    .btn-action { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 6px; color: white; border: none; transition: all 0.3s; }
    .btn-action:hover { transform: translateY(-2px); }
    .btn-action.btn-info { background-color: #3498db; }
    .btn-action.btn-warning { background-color: #f39c12; }
    .btn-action.btn-danger { background-color: #e74c3c; }
    .badge-status { padding: 0.4rem 0.8rem; border-radius: 20px; font-weight: 600; font-size: 0.85em; }
    .badge-active { background-color: #d4edda; color: #155724; }
    .badge-inactive { background-color: #f8d7da; color: #721c24; }
</style>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
        <h1>Gestión de Traslados</h1>
        @can('crear-traslado')
            <a href="{{ route('traslados.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nuevo Traslado
            </a>
        @endcan
    </div>

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Traslados</li>
    </ol>

    <div class="card">
        <div class="card-header">
            Lista de Traslados
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="modern-table" id="trasladosTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Usuario</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($traslados as $traslado)
                        <tr>
                            <td>{{ $traslado->id }}</td>
                            <td>{{ $traslado->fecha_hora->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $traslado->origenAlmacen?->nombre ?? 'N/A' }}</td>
                            <td>{{ $traslado->destinoAlmacen?->nombre ?? 'N/A' }}</td>
                            <td>{{ $traslado->user?->name ?? 'N/A' }}</td>
                            <td>
                                @if ($traslado->estado == 1)
                                    <span class="badge-status badge-active">Activo</span>
                                @else
                                    <span class="badge-status badge-inactive">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="action-btns-modern">
                                    @can('ver-traslado')
                                        <button class="btn-action btn-info" onclick="verTraslado({{ $traslado->id }})" title="Ver Detalle">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endcan
                                    @can('editar-traslado')
                                        <a href="{{ route('traslados.edit', $traslado) }}" class="btn-action btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('eliminar-traslado')
                                        <button class="btn-action btn-danger" onclick="eliminarTraslado({{ $traslado->id }})" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        <!-- Modal para ver detalles -->
                        <div class="modal fade" id="verTrasladoModal-{{ $traslado->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content" id="modalContent-{{ $traslado->id }}">
                                    <!-- Contenido dinámico -->
                                </div>
                            </div>
                        </div>

                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-inbox" style="font-size: 2rem; color: #ccc;"></i>
                                <p class="text-muted mt-2">No hay traslados registrados</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando {{ $traslados->firstItem() ?? 0 }} a {{ $traslados->lastItem() ?? 0 }} de {{ $traslados->total() ?? 0 }} registros
                </div>
                <div>
                    {{ $traslados->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
<script>
if (document.getElementById("trasladosTable")) {
    new simpleDatatables.DataTable("#trasladosTable", {
        searchable: true,
        fixedHeight: false,
        perPage: 10
    });
}

function verTraslado(id) {
    fetch(`/traslados/${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`modalContent-${id}`).innerHTML = data.html;
                const modal = new bootstrap.Modal(document.getElementById(`verTrasladoModal-${id}`));
                modal.show();
            }
        })
        .catch(() => Swal.fire('Error', 'No se pudo cargar los detalles', 'error'));
}

function eliminarTraslado(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: 'El traslado se eliminará permanentemente',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/traslados/${id}`;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
