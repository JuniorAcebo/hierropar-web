@extends('layouts.app')

@section('title', 'Compras')

@push('css-datatable')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 0.5rem;
            font-weight: 700;
            border: none;
            font-size: 1.2rem;
        }

        .card-body {
            background-color: #fafbfc;
            padding: 0.5rem;
        }

        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            font-size: 0.9em;
        }

        .modern-table thead {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        }

        .modern-table th {
            color: #ecf0f1;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.85em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        .modern-table td {
            padding: 1rem;
            text-align: left;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f4;
            background: #fff;
            transition: all 0.3s ease;
        }

        .modern-table tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .modern-table tbody tr:hover td {
            background-color: #f8f9fa;
        }

        /* BOTONES DE ACCIÓN MEJORADOS */
        .action-btns-modern {
            display: flex;
            gap: 6px;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .btn-view {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }

        .btn-edit {
            background: linear-gradient(135deg, #27ae60, #229954);
        }

        .btn-delete {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }

        .btn-pdf {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
        }

        .btn-action i {
            font-size: 0.8em;
        }

        /* BOTÓN PRIMARIO MEJORADO */
        .btn-primary-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        /* ESTILOS PARA EL CONTENIDO DE LA TABLA */
        .comprobante-info {
            line-height: 1.3;
        }

        .comprobante-tipo {
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.2em;
        }

        .comprobante-numero {
            color: #7f8c8d;
            font-size: 1.2em;
        }

        .proveedor-info {
            line-height: 1.3;
        }

        .proveedor-tipo {
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.2em;
            text-transform: capitalize;
        }

        .proveedor-nombre {
            color: #7f8c8d;
            font-size: 1.2em;
        }

        .fecha-info {
            line-height: 1.3;
        }

        .fecha-dia {
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.2em;
        }

        .fecha-hora {
            color: #7f8c8d;
            font-size: 1.2em;
        }

        .total-amount {
            font-weight: 700;
            color: #27ae60;
            font-size: 1.2em;
        }

        /* BREADCRUMB MEJORADO */
        .breadcrumb-modern {
            background-color: transparent;
            padding: 0.75rem 0;
            margin-bottom: 1rem;
        }

        .breadcrumb-modern .breadcrumb-item a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-modern .breadcrumb-item.active {
            color: #764ba2;
            font-weight: 600;
        }

        /* MODALES */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
            padding: 0.5rem;
        }

        .modal-footer .btn {
            border-radius: 8px;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
        }

        /* RESPONSIVE MEJORADO */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .modern-table {
                display: block;
                overflow-x: auto;
            }

            .modern-table th,
            .modern-table td {
                padding: 0.8rem 0.6rem;
                font-size: 0.85em;
            }

            .action-btns-modern {
                gap: 4px;
            }

            .btn-action {
                width: 28px;
                height: 28px;
            }

            .btn-action i {
                font-size: 0.7em;
            }

            h1 {
                font-size: 1.4rem;
            }
        }

        @media (max-width: 576px) {
            .comprobante-info,
            .proveedor-info,
            .fecha-info {
                font-size: 0.8em;
            }

            .btn-primary-modern {
                width: 100%;
                padding: 0.8rem 1.5rem;
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
    </style>
@endpush

@section('content')
    @include('layouts.partials.alert')

    <div class="container-fluid px-3 px-md-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0" style="color: #2c3e50; font-weight: 700;">Gestión de Compras</h1>
            @can('crear-compra')
                <a href="{{ route('compras.create') }}" class="btn btn-primary-modern">
                    <i class="fas fa-plus me-2"></i>Nueva Compra
                </a>
            @endcan
        </div>

        <ol class="breadcrumb breadcrumb-modern mb-4">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Compras</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                Lista de Compras
            </div>
            <div class="card-body">
                <table id="datatablesSimple" class="modern-table">
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Proveedor</th>
                            <th>Fecha y Hora</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($compras as $item)
                            <tr>
                                <td>
                                    <div class="comprobante-info">
                                        <div class="comprobante-tipo">{{ $item->comprobante->tipo_comprobante }}</div>
                                        <div class="comprobante-numero">{{ $item->numero_comprobante }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="proveedor-info">
                                        <div class="proveedor-tipo">{{ ucfirst($item->proveedore->persona->tipo_persona) }}</div>
                                        <div class="proveedor-nombre">{{ $item->proveedore->persona->razon_social }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fecha-info">
                                        <div class="fecha-dia">
                                            <i class="fas fa-calendar-day me-1"></i>
                                            {{ \Carbon\Carbon::parse($item->fecha_hora)->format('d-m-Y') }}
                                        </div>
                                        <div class="fecha-hora">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($item->fecha_hora)->format('H:i') }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="total-amount">${{ number_format($item->total, 2) }}</span>
                                </td>
                                <td>
                                    <div class="action-btns-modern">
                                        @can('mostrar-compra')
                                            <button type="button" class="btn-action btn-view view-compra"
                                                    data-id="{{ $item->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#viewCompraModal" title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endcan

                                        @can('editar-compra')
                                            <a href="{{ route('compras.edit', ['compra' => $item]) }}"
                                               class="btn-action btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan

                                        @can('eliminar-compra')
                                            <button type="button" class="btn-action btn-delete"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#confirmModal-{{ $item->id }}"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal de confirmación de eliminación -->
                            <div class="modal fade" id="confirmModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirmar Eliminación</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>¿Está seguro de que desea eliminar esta compra?</p>
                                            <small class="text-muted">Esta acción no se puede deshacer.</small>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('compras.destroy', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
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

        <!-- Modal para ver compra -->
        <div class="modal fade" id="viewCompraModal" tabindex="-1" aria-labelledby="viewCompraModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalles de la Compra</h5>
                    </div>
                    <div class="modal-body" id="modalCompraContent">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando detalles de la compra...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" id="pdfButton" class="btn btn-danger" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Descargar PDF
                        </a>
                        <button id="printPdfButton" class="btn btn-primary">
                            <i class="fas fa-print me-1"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar DataTable
            const dataTable = new simpleDatatables.DataTable("#datatablesSimple", {
                perPage: 10,
                perPageSelect: [5, 10, 15, 20],
                labels: {
                    placeholder: "Buscar compras...",
                    perPage: "registros por página",
                    noRows: "No se encontraron compras",
                    info: "Mostrando {start} a {end} de {rows} compras"
                }
            });

            // Delegación de eventos para botones dinámicos
            document.body.addEventListener('click', function(e) {
                // Manejar clic en botón Ver
                if (e.target.classList.contains('view-compra') || e.target.closest('.view-compra')) {
                    const button = e.target.classList.contains('view-compra') ?
                        e.target : e.target.closest('.view-compra');
                    const compraId = button.dataset.id;

                    // Mostrar modal inmediatamente
                    const modal = new bootstrap.Modal(document.getElementById('viewCompraModal'));
                    modal.show();

                    // Cargar contenido
                    loadCompraDetails(compraId);
                }

                // Manejar clic en botón Imprimir
                if (e.target.closest('#printPdfButton')) {
                    e.preventDefault();
                    const compraId = document.getElementById('pdfButton').href.split('/').pop();
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = `/compras/pdf/${compraId}?print=1`;
                    document.body.appendChild(iframe);

                    iframe.onload = function() {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    };
                }
            });

            function loadCompraDetails(compraId) {
                const modalContent = document.getElementById('modalCompraContent');
                const pdfButton = document.getElementById('pdfButton');

                modalContent.innerHTML = `
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando detalles de la compra...</p>
                    </div>
                `;

                fetch(`/compras/${compraId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Error al cargar los datos');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        modalContent.innerHTML = data.html;
                        pdfButton.href = `/compras/pdf/${compraId}`;
                    } else {
                        throw new Error(data.message || 'Error en los datos recibidos');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error al cargar los detalles: ${error.message}
                            <button class="btn btn-sm btn-warning mt-2" onclick="loadCompraDetails(${compraId})">
                                <i class="fas fa-redo me-1"></i>Reintentar
                            </button>
                        </div>
                    `;
                });
            }
        });
    </script>
@endpush
