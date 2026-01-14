@extends('layouts.app')

@section('title', 'Categorías')

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

        /* BADGE PARA DESCRIPCIÓN MEJORADO */
        .descripcion-modern {
            display: inline-block;
            background: linear-gradient(135deg, #f093fb, #f5576c);
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

        .descripcion-modern:hover {
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

        .btn-dropdown {
            background: linear-gradient(135deg, #f39c12, #e67e22);
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

        /* DROPDOWN MEJORADO */
        .dropdown-modern .dropdown-menu {
            min-width: 150px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: none;
            padding: 0.5rem 0;
            overflow: hidden;
        }

        .dropdown-modern .dropdown-item {
            padding: 0.6rem 1.2rem;
            font-size: 0.9em;
            color: #2c3e50;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .dropdown-modern .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #667eea;
            padding-left: 1.5rem;
        }

        .dropdown-modern .dropdown-item i {
            margin-right: 10px;
            width: 18px;
            text-align: center;
            font-size: 0.9em;
        }

        /* DESCRIPCIÓN TRUNCADA MEJORADA */
        .descripcion-truncada-modern {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
            line-height: 1.5;
            color: #7f8c8d;
            background: #f8f9fa;
            padding: 0.5rem 0.8rem;
            border-radius: 8px;
            border-left: 3px solid #3498db;
            font-size: 0.9em;
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

            .descripcion-truncada-modern {
                max-width: 200px;
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

            .descripcion-truncada-modern {
                max-width: 150px;
                font-size: 0.85em;
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

        /* NOMBRE DESTACADO */
        .nombre-destacado {
            font-weight: 700;
            color: #2c3e50;
            position: relative;
            padding-left: 1rem;
        }

        .nombre-destacado::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 70%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 2px;
        }
    </style>
@endpush

@section('content')
    @include('layouts.partials.alert')

    <div class="container-fluid px-4">
        <h1 class="mt-4 text-center" style="color: #2c3e50; font-weight: 700;">Gestión de Categorías</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}" style="text-decoration: none;">Inicio</a></li>
            <li class="breadcrumb-item active" style="color: #667eea;">Categorías</li>
        </ol>

        @can('crear-categoria')
            <div class="mb-4">
                <a href="{{route('categorias.create')}}" class="btn btn-primary-modern">
                    <i class="fas fa-plus me-2"></i> Añadir Nueva Categoría
                </a>
            </div>
        @endcan

        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-list me-2"></i>
                Lista de Categorías
            </div>
            <div class="card-body">
                <div class="datatable-wrapper">
                    <table id="datatablesSimple" class="modern-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categorias as $categoria)
                                <tr>
                                    <td>
                                        <span class="nombre-destacado">{{$categoria->caracteristica->nombre}}</span>
                                    </td>
                                    <td>
                                        @if($categoria->caracteristica->descripcion)
                                            <div class="descripcion-truncada-modern" title="{{$categoria->caracteristica->descripcion}}">
                                                {{$categoria->caracteristica->descripcion}}
                                            </div>
                                        @else
                                            <span class="text-muted fst-italic">Sin descripción</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($categoria->caracteristica->estado == 1)
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
                                            <!-- Dropdown para opciones -->
                                            <div class="dropdown dropdown-modern">
                                                <button class="btn-action btn-dropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Opciones">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @can('editar-categoria')
                                                    <li>
                                                        <a class="dropdown-item" href="{{route('categorias.edit',['categoria'=>$categoria])}}">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
                                                    </li>
                                                    @endcan

                                                    <!-- Botón Ver Detalles -->
                                                    @can('ver-categoria')
                                                    <li>
                                                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#verModal-{{$categoria->id}}">
                                                            <i class="fas fa-eye"></i> Ver Detalles
                                                        </button>
                                                    </li>
                                                    @endcan
                                                </ul>
                                            </div>

                                            <!-- Botón Eliminar/Restaurar -->
                                            @can('eliminar-categoria')
                                                <button class="btn-action {{ $categoria->caracteristica->estado ? 'btn-delete' : 'btn-restore' }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#confirmModal-{{$categoria->id}}"
                                                        title="{{ $categoria->caracteristica->estado ? 'Eliminar' : 'Restaurar' }}">
                                                    <i class="{{ $categoria->caracteristica->estado ? 'fas fa-trash' : 'fas fa-rotate-left' }}"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal de detalles -->
                                <div class="modal fade" id="verModal-{{$categoria->id}}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-info-circle me-2"></i>Detalles de la Categoría
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p><span class="fw-bold">ID:</span> {{ $categoria->id }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><span class="fw-bold">Estado:</span>
                                                            @if ($categoria->caracteristica->estado == 1)
                                                                <span class="badge-modern badge-activo">Activo</span>
                                                            @else
                                                                <span class="badge-modern badge-eliminado">Eliminado</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <p class="fw-bold">Nombre:</p>
                                                        <div class="p-3 bg-light rounded">
                                                            {{ $categoria->caracteristica->nombre }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12">
                                                        <p class="fw-bold">Descripción:</p>
                                                        <div class="p-3 bg-light rounded" style="min-height: 80px;">
                                                            {{ $categoria->caracteristica->descripcion ?? 'Sin descripción' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                @can('editar-categoria')
                                                <a href="{{route('categorias.edit',['categoria'=>$categoria])}}" class="btn btn-primary-modern">
                                                    <i class="fas fa-edit me-1"></i> Editar
                                                </a>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal de confirmación -->
                                <div class="modal fade" id="confirmModal-{{$categoria->id}}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmación
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center py-4">
                                                <i class="fas {{ $categoria->caracteristica->estado ? 'fa-trash-alt' : 'fa-rotate-left' }} fa-3x mb-3"
                                                   style="color: {{ $categoria->caracteristica->estado ? '#e74c3c' : '#27ae60' }};"></i>
                                                <h5 class="mb-3">
                                                    {{ $categoria->caracteristica->estado == 1 ? '¿Eliminar categoría?' : '¿Restaurar categoría?' }}
                                                </h5>
                                                <p class="text-muted">
                                                    {{ $categoria->caracteristica->estado == 1
                                                        ? 'La categoría "' . $categoria->caracteristica->nombre . '" se marcará como eliminada.'
                                                        : 'La categoría "' . $categoria->caracteristica->nombre . '" se restaurará y estará disponible nuevamente.' }}
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-1"></i> Cancelar
                                                </button>
                                                <form action="{{ route('categorias.destroy',['categoria'=>$categoria->id]) }}" method="post">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit" class="btn {{ $categoria->caracteristica->estado ? 'btn-danger' : 'btn-success' }}">
                                                        <i class="fas {{ $categoria->caracteristica->estado ? 'fa-trash' : 'fa-rotate-left' }} me-1"></i>
                                                        {{ $categoria->caracteristica->estado ? 'Eliminar' : 'Restaurar' }}
                                                    </button>
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
                    perPageSelect: [5, 10, 15, 20, 25],
                    searchable: true,
                    labels: {
                        placeholder: "Buscar categorías...",
                        perPage: "Mostrar {select} registros",
                        noRows: "No se encontraron categorías",
                        info: "Mostrando {start} a {end} de {rows} categorías"
                    }
                });
            }
        });
    </script>
@endpush
