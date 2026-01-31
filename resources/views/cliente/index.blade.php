@extends('layouts.app')

@section('title', 'Clientes')

@push('css-datatable')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 0.9rem;
        }

        .card-clean {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #fff;
        }

        .card-header-clean {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header-title {
            font-weight: 600;
            font-size: 1rem;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
        }

        .custom-table thead th {
            background: #fff;
            color: #6c757d;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid #dee2e6;
        }

        .custom-table tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
            color: #495057;
            font-size: 0.9rem;
        }

        .custom-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #e9ecef;
            color: #495057;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
            margin-right: 10px;
        }

        .client-info {
            display: flex;
            align-items: center;
        }

        .info-subtext {
            display: block;
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 2px;
        }

        .badge-pill {
            padding: 0.35em 0.65em;
            border-radius: 50rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-success { 
            background-color: #d4edda; 
            color: #155724; 
        }
        .badge-danger { 
            background-color: #f8d7da; 
            color: #721c24; 
        }

        .btn-action-group {
            display: flex;
            gap: 4px;
        }

        .btn-icon-soft {
            width: 28px;
            height: 28px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.15s;
            background: transparent;
            color: #6c757d;
        }

        .btn-icon-soft:hover {
            background-color: #e9ecef;
            color: #495057;
        }

        .btn-icon-soft.delete:hover {
            background-color: #f8d7da;
            color: #721c24;
        }

        .btn-create {
            background: #495057;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: none;
            font-weight: 500;
            transition: all 0.15s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
        }

        .btn-create:hover {
            background: #343a40;
            color: white;
        }

        /* Modal */
        .modal-content-clean {
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }

        .modal-header-clean {
            background: #f8f9fa;
            color: #495057;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem;
        }
    </style>
@endpush

@section('content')
    @include('layouts.partials.alert')

    <div class="container-fluid px-4 py-4">
        
        <div class="page-header">
            <div>
                <h1 class="page-title">Clientes</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('panel') }}" class="text-decoration-none text-muted">Inicio</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Clientes</li>
                    </ol>
                </nav>
            </div>
            @can('crear-cliente')
            <a href="{{ route('clientes.create') }}" class="btn-create">
                <i class="fas fa-plus"></i> Nuevo Cliente
            </a>
            @endcan
        </div>

        <div class="card-clean">
            <div class="card-header-clean">
                <div class="card-header-title">
                    <i class="fas fa-list"></i> Lista de Clientes
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="datatablesSimple" class="custom-table">
                        <thead>
                            <tr>
                                <th>Cliente / Razón Social</th>
                                <th>Contacto</th>
                                <th>Documento</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $item)
                                <tr>
                                    <td>
                                        <div class="client-info">
                                            <div class="user-avatar">
                                                {{ strtoupper(substr($item->persona->razon_social, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $item->persona->razon_social }}</div>
                                                <span class="info-subtext">{{ Str::limit($item->persona->direccion, 30) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $item->persona->telefono ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $item->persona->documento->tipo_documento }}</div>
                                        <span class="info-subtext">{{ $item->persona->numero_documento }}</span>
                                    </td>
                                    <td>
                                        @if($item->persona->tipo_persona == 'natural')
                                            <span class="badge bg-light text-dark border">Natural</span>
                                        @else
                                            <span class="badge bg-light text-dark border">Jurídica</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->persona->estado == 1)
                                            <span class="badge-pill badge-success">Activo</span>
                                        @else
                                            <span class="badge-pill badge-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-action-group">
                                            @can('editar-cliente')
                                            <a href="{{ route('clientes.edit', ['cliente' => $item]) }}" class="btn-icon-soft" title="Editar">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            @endcan

                                            @can('eliminar-cliente')
                                                @if ($item->persona->estado == 1)
                                                    <button class="btn-icon-soft delete" data-bs-toggle="modal"
                                                        data-bs-target="#confirmModal-{{ $item->id }}" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <button class="btn-icon-soft" data-bs-toggle="modal"
                                                        data-bs-target="#confirmModal-{{ $item->id }}" title="Restaurar">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                @endif
                                            @endcan
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal -->
                                <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content modal-content-clean">
                                            <div class="modal-header modal-header-clean">
                                                <h5 class="modal-title fs-6">Confirmar acción</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4 text-center">
                                                <h6 class="mb-3">{{ $item->persona->estado == 1 ? '¿Eliminar cliente?' : '¿Restaurar cliente?' }}</h6>
                                                <p class="text-muted small mb-4">
                                                    {{ $item->persona->estado == 1 
                                                        ? 'El cliente se desactivará del sistema.' 
                                                        : 'El cliente volverá a estar activo.' }}
                                                </p>
                                                
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('clientes.destroy', ['cliente' => $item->persona->id]) }}" method="post">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit" class="btn {{ $item->persona->estado == 1 ? 'btn-outline-danger' : 'btn-outline-success' }} btn-sm">
                                                            {{ $item->persona->estado == 1 ? 'Eliminar' : 'Restaurar' }}
                                                        </button>
                                                    </form>
                                                </div>
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
                    labels: {
                        placeholder: "Buscar...",
                        perPage: "por página",
                        noRows: "No hay registros",
                        info: "Mostrando {start} a {end} de {rows}"
                    }
                });
            }
        });
    </script>
@endpush