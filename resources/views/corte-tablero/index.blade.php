@extends('layouts.app')

@section('title','Cortes de Tablero')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Estilos consistentes con el sistema */
    .card-header {
        background-color: #34495e;
        color: #fff;
        padding: 1rem 1.5rem;
        font-size: 1.25rem;
        font-weight: 600;
        border-bottom: 2px solid #2c3e50;
    }

    .card-body {
        background-color: #f4f6f9;
        padding: 1.5rem;
    }

    /* Tabla unificada */
    .light-table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        font-size: 0.95em;
    }

    .light-table thead {
        background-color: #2c3e50;
        color: #ecf0f1;
    }

    .light-table th,
    .light-table td {
        padding: 14px 18px;
        text-align: left;
    }

    .light-table tbody tr {
        border-bottom: 1px solid #e1e1e1;
        transition: background-color 0.2s ease;
    }

    .light-table tbody tr:nth-child(even) {
        background-color: #f9fbfc;
    }

    .light-table tbody tr:hover {
        background-color: #f1f4f8;
    }

    /* Badges de estado */
    .badge-estado {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
        border-radius: 4px;
        font-weight: 500;
    }

    .badge-pendiente {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-en_proceso {
        background-color: #17a2b8;
        color: white;
    }

    .badge-completado {
        background-color: #28a745;
        color: white;
    }

    /* Botones de acción */
    .action-btns {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: transparent;
        border: none;
        color: #2c3e50;
        transition: all 0.3s;
    }

    .action-btn:hover {
        background-color: #e1e1e1;
        transform: scale(1.1);
    }

    .action-btn i {
        font-size: 0.9em;
    }

    .dropdown-menu {
        min-width: 120px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: none;
    }

    .dropdown-item {
        padding: 0.35rem 1rem;
        font-size: 0.9em;
    }

    .dropdown-item i {
        margin-right: 8px;
        width: 16px;
        text-align: center;
    }

    /* Botón primario consistente */
    .btn-primary {
        background-color: #3498db;
        border: none;
        padding: 8px 16px;
        border-radius: 5px;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background-color: #2980b9;
        transform: translateY(-1px);
    }

    /* Descripción truncada */
    .descripcion-truncada {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 250px;
    }

    /* Medidas y números */
    .medida {
        font-family: 'Courier New', monospace;
        background-color: #f8f9fa;
        padding: 2px 6px;
        border-radius: 3px;
        border: 1px solid #e9ecef;
    }

    /* Responsive */
    @media screen and (max-width: 768px) {
        .light-table {
            display: block;
            overflow-x: auto;
        }

        .action-btns {
            flex-wrap: wrap;
        }

        .descripcion-truncada {
            max-width: 150px;
        }
    }
</style>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Cortes de Tablero</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Cortes de Tablero</li>
    </ol>

    @can('crear-corte-tablero')
    <div class="mb-4">
        <a href="{{ route('cortes-tablero.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Corte de Tablero
        </a>
    </div>
    @endcan

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-cut me-1"></i>
            Lista de Cortes de Tablero
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="light-table">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Trabajo</th>
                        <th>Medidas Tablero</th>
                        <th>Piezas</th>
                        <th>Cortes</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cortes as $corte)
                    <tr>
                        <td class="fw-semibold">
                            {{ $corte->cliente->persona->razon_social }}
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $corte->nombre_trabajo }}</div>
                            <small class="text-muted descripcion-truncada" title="{{ $corte->descripcion }}">
                                {{ $corte->descripcion ?: 'Sin descripción' }}
                            </small>
                        </td>
                        <td>
                            <span class="medida">{{ $corte->largo_tablero }}x{{ $corte->ancho_tablero }} cm</span>
                            <br>
                            <small class="text-muted">x{{ $corte->cantidad_tableros }} tableros</small>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $corte->total_piezas }}</span> piezas
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $corte->total_cortes }}</span> cortes
                        </td>
                        <td>
                            @switch($corte->estado)
                                @case('pendiente')
                                    <span class="badge-estado badge-pendiente">Pendiente</span>
                                    @break
                                @case('en_proceso')
                                    <span class="badge-estado badge-en_proceso">En Proceso</span>
                                    @break
                                @case('completado')
                                    <span class="badge-estado badge-completado">Completado</span>
                                    @break
                            @endswitch
                        </td>
                        <td>
                            <div class="action-btns">
                                @can('mostrar-corte-tablero')
                                <a href="{{ route('cortes-tablero.show', $corte) }}" class="action-btn" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('editar-corte-tablero')
                                <a href="{{ route('cortes-tablero.edit', $corte) }}" class="action-btn" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @can('eliminar-corte-tablero')
                                <button class="action-btn" data-bs-toggle="modal" data-bs-target="#confirmModal-{{ $corte->id }}" title="Eliminar">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>

                    <!-- Modal de confirmación -->
                    <div class="modal fade" id="confirmModal-{{ $corte->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirmar Eliminación</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Seguro que quieres eliminar el corte de tablero "{{ $corte->nombre_trabajo }}"?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <form action="{{ route('cortes-tablero.destroy', $corte) }}" method="post">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataTable = new simpleDatatables.DataTable("#datatablesSimple", {
            classes: {
                table: "light-table",
                tr: "light-table-row",
                th: "light-table-header",
                td: "light-table-cell"
            },
            perPage: 10,
            perPageSelect: [5, 10, 15, 20]
        });
    });
</script>
@endpush
