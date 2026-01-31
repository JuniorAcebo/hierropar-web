@extends('layouts.app')

@section('title', 'Ventas')

@push('css-datatable')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* DISEÑO MODERNO CON FUENTES MÁS LEGIBLES */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 0.8rem 1rem;
            font-weight: 700;
            border: none;
            font-size: 1.2rem; /* Tamaño aumentado */
        }

        .card-body {
            background-color: #fafbfc;
            padding: 1rem;
        }

        /* TABLA MODERNA CON FUENTES MÁS GRANDES */
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            font-size: 0.95em; /* Aumentado de 0.85em */
        }

        .modern-table thead {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        }

        .modern-table th {
            color: #ecf0f1;
            padding: 0.8rem 0.8rem; /* Padding aumentado */
            text-align: left;
            font-weight: 600;
            font-size: 0.9em; /* Aumentado de 0.8em */
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: none;
        }

        .modern-table td {
            padding: 0.8rem 0.8rem; /* Padding aumentado */
            text-align: left;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f4;
            background: #fff;
            transition: all 0.3s ease;
        }

        .modern-table tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .modern-table tbody tr:hover td {
            background-color: #f8f9fa;
        }

        /* BOTONES DE ACCIÓN CON MEJOR LEGIBILIDAD */
        .action-btns-modern {
            display: flex;
            gap: 5px; /* Espacio aumentado */
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px; /* Tamaño aumentado */
            height: 32px; /* Tamaño aumentado */
            border-radius: 6px;
            border: none;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
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
            font-size: 0.85em; /* Tamaño de icono aumentado */
        }

        /* BOTÓN PRIMARIO CON MEJOR LEGIBILIDAD */
        .btn-primary-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.6rem 1.3rem; /* Padding aumentado */
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.95rem; /* Tamaño de fuente aumentado */
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);
        }

        .btn-primary-modern:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(102, 126, 234, 0.4);
        }

        .comprobante-info {
            line-height: 1.3;
        }

        .comprobante-tipo {
            font-weight: 600;
            color: #2c3e50;
            font-size: 1em;
        }

        .comprobante-numero {
            color: #7f8c8d;
            font-size: 1em;
        }

        .cliente-info {
            line-height: 1.3;
        }

        .cliente-nombre {
            font-weight: 600;
            color: #2c3e50;
            font-size: 1em;
        }

        .cliente-documento {
            color: #7f8c8d;
            font-size: 1em;
        }

        .fecha-info {
            line-height: 1.3;
        }

        .fecha-dia {
            font-weight: 600;
            color: #2c3e50;
            font-size: 1em;
        }

        .fecha-hora {
            color: #7f8c8d;
            font-size: 1em;
        }

        .total-amount {
            font-weight: 700;
            color: #27ae60;
            font-size: 1em;
        }

        .badge-modern {
            font-size: 0.82em;
            padding: 0.35em 0.7em;
            border-radius: 12px;
            font-weight: 600;
            display: inline-block;
            text-align: center;
            min-width: 65px;
        }

        .badge-activo {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .badge-anulado {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        /* BREADCRUMB CON MEJOR LEGIBILIDAD */
        .breadcrumb-modern {
            background-color: transparent;
            padding: 0.6rem 0; /* Padding aumentado */
            margin-bottom: 0.8rem;
        }

        .breadcrumb-modern .breadcrumb-item a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem; /* Aumentado de 0.9rem */
        }

        .breadcrumb-modern .breadcrumb-item.active {
            color: #764ba2;
            font-weight: 600;
            font-size: 0.95rem; /* Aumentado de 0.9rem */
        }

        /* MODALES CON MEJOR LEGIBILIDAD */
        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
            padding: 0.9rem 1rem; /* Padding aumentado */
        }

        .modal-footer .btn {
            border-radius: 6px;
            font-weight: 600;
            padding: 0.6rem 1.1rem; /* Padding aumentado */
            font-size: 0.9rem; /* Tamaño de fuente aumentado */
        }

        /* ENCABEZADO CON MEJOR LEGIBILIDAD */
        h1 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.5rem; /* Aumentado de 1.4rem */
            margin-bottom: 0.5rem;
        }

        /* RESPONSIVE CON TAMAÑOS AJUSTADOS */
        @media (max-width: 768px) {
            .card-body {
                padding: 0.8rem;
            }

            .modern-table {
                display: block;
                overflow-x: auto;
                font-size: 0.9em; /* Tamaño mantenido legible */
            }

            .modern-table th,
            .modern-table td {
                padding: 0.7rem 0.6rem; /* Padding mantenido */
                font-size: 0.85em; /* Tamaño legible */
            }

            .action-btns-modern {
                gap: 4px;
            }

            .btn-action {
                width: 30px; /* Tamaño mantenido */
                height: 30px; /* Tamaño mantenido */
            }

            .btn-action i {
                font-size: 0.8em; /* Tamaño mantenido */
            }

            h1 {
                font-size: 1.3rem; /* Tamaño mantenido legible */
            }

            .btn-primary-modern {
                width: 100%;
                padding: 0.7rem 1rem; /* Padding aumentado */
            }
        }

        @media (max-width: 576px) {
            .comprobante-info,
            .cliente-info,
            .fecha-info {
                font-size: 0.85em; /* Tamaño mínimo legible */
            }

            .badge-modern {
                min-width: 60px; /* Ancho mantenido */
                font-size: 0.8em; /* Tamaño mínimo legible */
            }
        }

        /* ANIMACIONES SUAVES */
        .modern-table tbody tr {
            animation: fadeInUp 0.4s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ESTADO DESHABILITADO */
        .btn-action:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        /* MEJORA EN LA LEGIBILIDAD GENERAL */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .text-muted {
            color: #6c757d !important;
            font-size: 0.9em; /* Tamaño aumentado para mejor legibilidad */
        }
    </style>
@endpush

@section('content')
    @include('layouts.partials.alert')

    <div class="container-fluid px-3 px-md-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Gestión de Ventas</h1>
            @can('crear-venta')
                <a href="{{ route('ventas.create') }}" class="btn btn-primary-modern">
                    <i class="fas fa-plus me-1"></i>Nueva Venta
                </a>
            @endcan
        </div>

        <ol class="breadcrumb breadcrumb-modern mb-3">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Ventas</li>
        </ol>

        <div class="card mb-4">
            <div class="card-header">
                Registro de Ventas
            </div>
            <div class="card-body">
                <!-- Buscador Inteligente -->
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="searchVentas" class="form-control" placeholder="Buscar por comprobante, cliente, total, fecha...">
                    </div>
                </div>

                <table id="datatablesVentas" class="modern-table">
                    <thead>
                        <tr>
                            <th>Comprobante</th>
                            <th>Cliente</th>
                            <th>Fecha/Hora</th>
                            <th>Total</th>
                            <th>Estado Pago</th>
                            <th>Estado Entrega</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ventas as $venta)
                            <tr>
                                <td>
                                    <div class="comprobante-info">
                                        <div class="comprobante-tipo">{{ $venta->comprobante->tipo_comprobante }}</div>
                                        <div class="comprobante-numero">{{ $venta->numero_comprobante }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="cliente-info">
                                        @if ($venta->cliente && $venta->cliente->persona)
                                            <div class="cliente-nombre">{{ $venta->cliente->persona->razon_social }}</div>
                                            <div class="cliente-documento">{{ $venta->cliente->persona->numero_documento }}</div>
                                        @else
                                            <div class="cliente-nombre text-danger">Cliente no disponible</div>
                                            <div class="cliente-documento">---</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fecha-info">
                                        <div class="fecha-dia">
                                            <i class="fas fa-calendar-day me-1"></i>
                                            {{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d-m-Y') }}
                                        </div>
                                        <div class="fecha-hora">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($venta->fecha_hora)->format('H:i') }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="total-amount">S/ {{ number_format($venta->total, 2) }}</span>
                                </td>
                                <td>
                                    @if ($venta->estado_pago == 'pendiente' || $venta->estado_pago == 0)
                                        <span class="badge-modern badge-anulado">Pendiente</span>
                                    @else
                                        <span class="badge-modern badge-activo">Pagado</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($venta->estado_entrega == 'pendiente' || $venta->estado_entrega == 0)
                                        <span class="badge-modern badge-anulado">Pendiente</span>
                                    @else
                                        <span class="badge-modern badge-activo">Entregado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-btns-modern">
                                        @can('mostrar-venta')
                                            <button type="button" class="btn-action btn-view view-venta"
                                                    data-id="{{ $venta->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#viewVentaModal" title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        @endcan

                                        @can('editar-venta')
                                            <a href="{{ route('ventas.edit', $venta->id ) }}"
                                               class="btn-action btn-edit {{ $venta->estado != 1 ? 'disabled' : '' }}"
                                               title="Editar"
                                               @if($venta->estado != 1) onclick="return false;" style="pointer-events: none;" @endif>
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan

                                        @can('eliminar-venta')
                                            <button type="button" class="btn-action btn-delete btn-delete-venta"
                                                    data-id="{{ $venta->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#confirmModal" title="Anular"
                                                    @if($venta->estado != 1) disabled @endif>
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @endcan

                                        @can('generar-pdf-venta')
                                            <a href="{{ route('ventas.pdf', $venta->id) }}"
                                               class="btn-action btn-pdf" title="Descargar PDF" target="_blank">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-receipt fa-2x mb-3"></i>
                                        <p>No hay ventas registradas aún.</p>
                                        @can('crear-venta')
                                            <a href="{{ route('ventas.create') }}" class="btn btn-primary-modern btn-sm">
                                                <i class="fas fa-plus me-1"></i> Registrar primera venta
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para anulación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">Confirmar Anulación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea anular esta venta?</p>
                    <small class="text-muted">Esta acción revertirá el stock de los productos.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Confirmar Anulación</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver venta -->
    <div class="modal fade" id="viewVentaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Venta</h5>
                </div>
                <div class="modal-body" id="modalVentaContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando detalles de la venta...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" id="ventaPdfLink" class="btn btn-danger" target="_blank">
                        <i class="fas fa-file-pdf me-1"></i> Descargar PDF
                    </a>
                    <button id="printVentaButton" class="btn btn-primary">
                        <i class="fas fa-print me-1"></i> Imprimir
                    </button>
                    <button id="marcarPagadoButton" class="btn btn-success" title="Marcar como pagado">
                        <i class="fas fa-money-bill me-1"></i> Pagar
                    </button>
                    <button id="marcarEntregadoButton" class="btn btn-info text-white" title="Marcar como entregado">
                        <i class="fas fa-truck me-1"></i> Entregado
                    </button>
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
            const dataTable = new simpleDatatables.DataTable("#datatablesVentas", {
                perPage: 10,
                perPageSelect: [5, 10, 15, 20],
                labels: {
                    placeholder: "Buscar ventas...",
                    perPage: "{select} registros por página",
                    noRows: "No se encontraron ventas",
                    info: "Mostrando {start} a {end} de {rows} ventas"
                }
            });

            // Buscador inteligente
            const searchInput = document.getElementById('searchVentas');
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    dataTable.search(searchTerm);
                });
            }

            // Delegación de eventos para botones dinámicos
            document.body.addEventListener('click', function(e) {
                // Manejar clic en botón Ver
                if (e.target.classList.contains('view-venta') || e.target.closest('.view-venta')) {
                    const button = e.target.classList.contains('view-venta') ?
                        e.target : e.target.closest('.view-venta');
                    const ventaId = button.dataset.id;

                    // Mostrar modal inmediatamente
                    const modal = new bootstrap.Modal(document.getElementById('viewVentaModal'));
                    modal.show();

                    // Cargar contenido
                    loadVentaDetails(ventaId);
                }

                // Manejar clic en botón Anular
                if (e.target.classList.contains('btn-delete-venta') || e.target.closest('.btn-delete-venta')) {
                    const button = e.target.classList.contains('btn-delete-venta') ?
                        e.target : e.target.closest('.btn-delete-venta');
                    const ventaId = button.dataset.id;
                    document.getElementById('deleteForm').action = `/ventas/${ventaId}`;
                }

                // Manejar clic en botón Imprimir
                if (e.target.closest('#printVentaButton')) {
                    e.preventDefault();
                    const ventaId = document.getElementById('viewVentaModal').dataset.currentVenta;
                    const iframe = document.createElement('iframe');
                    iframe.style.display = 'none';
                    iframe.src = `/ventas/pdf/${ventaId}?print=1`;
                    document.body.appendChild(iframe);

                    iframe.onload = function() {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    };
                }

                // Manejar clic en botón Pagar
                if (e.target.closest('#marcarPagadoButton')) {
                    e.preventDefault();
                    const ventaId = document.getElementById('viewVentaModal').dataset.currentVenta;
                    actualizarEstadoPago(ventaId, 'pagado');
                }

                // Manejar clic en botón Entrega
                if (e.target.closest('#marcarEntregadoButton')) {
                    e.preventDefault();
                    const ventaId = document.getElementById('viewVentaModal').dataset.currentVenta;
                    actualizarEstadoEntrega(ventaId, 'entregado');
                }
            });

            function loadVentaDetails(ventaId) {
                const modalContent = document.getElementById('modalVentaContent');
                const modal = document.getElementById('viewVentaModal');

                modal.dataset.currentVenta = ventaId;
                modalContent.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando detalles de la venta...</p>
                    </div>
                `;

                fetch(`/ventas/${ventaId}`, {
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
                        document.getElementById('ventaPdfLink').href = `/ventas/pdf/${ventaId}`;

                        // Actualizar estado de los botones según el estado de pago y entrega
                        const marcarPagadoBtn = document.getElementById('marcarPagadoButton');
                        const marcarEntregadoBtn = document.getElementById('marcarEntregadoButton');

                        if (data.venta.estado_pago === 'pagado' || data.venta.estado_pago === 1) {
                            marcarPagadoBtn.disabled = true;
                            marcarPagadoBtn.innerHTML = '<i class="fas fa-check me-1"></i> Pagado';
                        } else {
                            marcarPagadoBtn.disabled = false;
                        }

                        if (data.venta.estado_entrega === 'entregado' || data.venta.estado_entrega === 1) {
                            marcarEntregadoBtn.disabled = true;
                            marcarEntregadoBtn.innerHTML = '<i class="fas fa-check me-1"></i> Entregado';
                        } else {
                            marcarEntregadoBtn.disabled = false;
                        }
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
                            <button class="btn btn-sm btn-warning mt-2" onclick="loadVentaDetails(${ventaId})">
                                <i class="fas fa-redo me-1"></i>Reintentar
                            </button>
                        </div>
                    `;
                });
            }

            function actualizarEstadoPago(ventaId, estado) {
                Swal.fire({
                    title: '¿Confirmar pago?',
                    text: 'Marcaré esta venta como pagada',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/ventas/${ventaId}/estado-pago`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ estado_pago: estado })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('¡Éxito!', data.message, 'success').then(() => {
                                    loadVentaDetails(ventaId);
                                });
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Error al actualizar el estado', 'error');
                        });
                    }
                });
            }

            function actualizarEstadoEntrega(ventaId, estado) {
                Swal.fire({
                    title: '¿Confirmar entrega?',
                    text: 'Marcaré esta venta como entregada',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#17a2b8',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, confirmar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/ventas/${ventaId}/estado-entrega`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ estado_entrega: estado })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('¡Éxito!', data.message, 'success').then(() => {
                                    loadVentaDetails(ventaId);
                                });
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Error al actualizar el estado', 'error');
                        });
                    }
                });
            }
        });
    </script>
@endpush
