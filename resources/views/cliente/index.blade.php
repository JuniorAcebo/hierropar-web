@extends('layouts.app')

@section('title', 'Clientes')

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

        .badge-activo {
            background-color: #28a745;
            color: white;
        }

        .badge-eliminado {
            background-color: #dc3545;
            color: white;
        }

        /* Documento */
        .documento-info {
            line-height: 1.2;
        }

        .documento-tipo {
            font-weight: 600;
            font-size: 0.95em;
        }

        .documento-numero {
            font-size: 0.85em;
            color: #6c757d;
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
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
        <h1 class="mt-4 text-center">Clientes</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Clientes</li>
        </ol>

        @can('crear-cliente')
            <div class="mb-4">
                <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Añadir nuevo registro
                </a>
            </div>
        @endcan

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Tabla de clientes
            </div>
            <div class="card-body">
                <table id="datatablesSimple" class="light-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Documento</th>
                            <th>Tipo de persona</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clientes as $item)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $item->persona->razon_social }}
                                </td>
                                <td>
                                    {{ $item->persona->direccion }}
                                </td>
                                <td>
                                    <div class="documento-info">
                                        <div class="documento-tipo">{{ $item->persona->documento->tipo_documento }}</div>
                                        <div class="documento-numero">{{ $item->persona->numero_documento }}</div>
                                    </div>
                                </td>
                                <td>
                                    {{ ucfirst($item->persona->tipo_persona) }}
                                </td>
                                <td>
                                    @if ($item->persona->estado == 1)
                                        <span class="badge-estado badge-activo">Activo</span>
                                    @else
                                        <span class="badge-estado badge-eliminado">Eliminado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <div class="dropdown">
                                            <button class="action-btn" data-bs-toggle="dropdown" aria-expanded="false"
                                                title="Opciones">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @can('editar-cliente')
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('clientes.edit', ['cliente' => $item]) }}">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
                                                    </li>
                                                @endcan
                                            </ul>
                                        </div>

                                        @can('eliminar-cliente')
                                            @if ($item->persona->estado == 1)
                                                <button class="action-btn" data-bs-toggle="modal"
                                                    data-bs-target="#confirmModal-{{ $item->id }}" title="Eliminar">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            @else
                                                <button class="action-btn" data-bs-toggle="modal"
                                                    data-bs-target="#confirmModal-{{ $item->id }}" title="Restaurar">
                                                    <i class="fas fa-rotate"></i>
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>

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
                                            {{ $item->persona->estado == 1 ? '¿Seguro que quieres eliminar este cliente?' : '¿Seguro que quieres restaurar este cliente?' }}
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('clientes.destroy', ['cliente' => $item->persona->id]) }}"
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
