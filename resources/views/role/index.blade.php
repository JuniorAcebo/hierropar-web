@extends('layouts.app')

@section('title','Roles')

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

    /* Badge de rol */
    .badge-rol {
        font-size: 0.9rem;
        padding: 0.4em 0.8em;
        border-radius: 4px;
        font-weight: 500;
        background-color: #6f42c1;
        color: white;
        display: inline-block;
    }

    /* Botones de acción */
    .action-btns {
        display: flex;
        gap: 10px;
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.85em;
        font-weight: 500;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
    }

    .btn-action i {
        margin-right: 6px;
    }

    .btn-edit {
        background-color: #ffc107;
        color: #212529;
        border: none;
    }

    .btn-edit:hover {
        background-color: #e0a800;
        transform: translateY(-1px);
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
        border: none;
    }

    .btn-delete:hover {
        background-color: #c82333;
        transform: translateY(-1px);
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

    /* Responsive */
    @media screen and (max-width: 768px) {
        .light-table {
            display: block;
            overflow-x: auto;
        }

        .action-btns {
            flex-direction: column;
            gap: 5px;
        }

        .btn-action {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Roles</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Roles</li>
    </ol>

    @can('crear-role')
    <div class="mb-4">
        <a href="{{route('roles.create')}}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Añadir nuevo rol
        </a>
    </div>
    @endcan

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Tabla de roles
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="light-table">
                <thead>
                    <tr>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $item)
                    <tr>
                        <td>
                            <span class="badge-rol">{{$item->name}}</span>
                        </td>
                        <td>
                            <div class="action-btns">
                                @can('editar-role')
                                <a href="{{route('roles.edit',['role'=>$item])}}" class="btn-action btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                @endcan

                                @can('eliminar-role')
                                <button type="button" class="btn-action btn-delete" data-bs-toggle="modal" data-bs-target="#confirmModal-{{$item->id}}" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>

                    <!-- Modal de confirmación -->
                    <div class="modal fade" id="confirmModal-{{$item->id}}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirmación</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Seguro que quieres eliminar este rol?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <form action="{{ route('roles.destroy',['role'=>$item->id]) }}" method="post">
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
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabla = document.querySelector("#datatablesSimple");
            if (tabla) {
                new simpleDatatables.DataTable(tabla, {
                    perPage: 10,
                    perPageSelect: [10, 25, 50, 100, -1], // -1 = todos
                    searchable: true,
                    labels: {
                        placeholder: "Buscar...",
                        perPage: "Mostrar {select} registros",
                        noRows: "No se encontraron resultados",
                        info: "Mostrando {start} a {end} de {rows} registros"
                    }
                });
            }
        });
    </script>
@endpush
