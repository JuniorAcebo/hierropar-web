@extends('layouts.app')

@section('title','Usuarios')

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

    /* Badges de rol */
    .badge-rol {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
        border-radius: 4px;
        font-weight: 500;
    }
    .badge-admin {
        background-color: #6f42c1;
        color: white;
    }
    .badge-user {
        background-color: #17a2b8;
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

    /* Responsive */
    @media screen and (max-width: 768px) {
        .light-table {
            display: block;
            overflow-x: auto;
        }

        .action-btns {
            flex-wrap: wrap;
        }
    }
</style>
@endpush

@section('content')
@include('layouts.partials.alert')

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Usuarios</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Usuarios</li>
    </ol>

    @can('crear-user')
    <div class="mb-4">
        <a href="{{route('users.create')}}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Añadir nuevo usuario
        </a>
    </div>
    @endcan

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Tabla de usuarios
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="light-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $item)
                    <tr>
                        <td class="fw-semibold">
                            {{$item->name}}
                        </td>
                        <td>
                            {{$item->email}}
                        </td>
                        <td>
                            @php $role = $item->getRoleNames()->first(); @endphp
                            <span class="badge-rol {{ $role == 'admin' ? 'badge-admin' : 'badge-user' }}">
                                {{ ucfirst($role) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-btns">
                                <div class="dropdown">
                                    <button class="action-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Opciones">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @can('editar-user')
                                        <li>
                                            <a class="dropdown-item" href="{{route('users.edit',['user'=>$item])}}">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>

                                @can('eliminar-user')
                                <button class="action-btn" data-bs-toggle="modal" data-bs-target="#confirmModal-{{$item->id}}" title="Eliminar">
                                    <i class="far fa-trash-alt"></i>
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
                                    ¿Seguro que quieres eliminar este usuario?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <form action="{{ route('users.destroy',['user'=>$item->id]) }}" method="post">
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
