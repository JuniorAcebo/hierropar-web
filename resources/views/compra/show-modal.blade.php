<style>
    .show-modal-content .border-section {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        background: #fff;
    }
    .show-modal-content .section-title {
        font-size: 15px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e9ecef;
    }
    .show-modal-content .section-title i {
        margin-right: 8px;
        color: #3498db;
    }
    .show-modal-content .label-title {
        font-weight: 500;
        font-size: 13px;
        color: #495057;
        margin-bottom: 2px;
    }
    .show-modal-content .value-text {
        font-size: 14px;
        color: #212529;
        font-weight: 500;
    }
    .show-modal-content .badge-modern {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    .show-modal-content .table th {
        font-size: 13px;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    .show-modal-content .table td {
        font-size: 13px;
        vertical-align: middle;
    }
</style>

<div class="container-fluid p-0 show-modal-content">
    <!-- Header del Modal personalizado si se desea, o usar el default -->
    
    <div class="border-section">
        <div class="row mb-3">
            <div class="col-md-6">
                <h5 class="mb-1 text-primary fw-bold">COMPRA #{{ $compra->numero_comprobante }}</h5>
                <p class="text-muted small mb-0">Registrado por: {{ $compra->user->name ?? 'Sistema' }}</p>
            </div>
            <div class="col-md-6 text-end">
                <div class="badge bg-light text-dark border">
                    <i class="far fa-clock me-1"></i> 
                    {{ \Carbon\Carbon::parse($compra->fecha_hora)->format('d/m/Y H:i A') }}
                </div>
            </div>
        </div>
        
        <div class="section-title">
            <i class="fas fa-info-circle"></i> Datos Generales
        </div>
        
        <div class="row g-3">
            <div class="col-md-4">
                <div class="label-title">Proveedor</div>
                <div class="value-text">{{ $compra->proveedor->persona->razon_social }}</div>
                <small class="text-muted">{{ $compra->proveedor->persona->tipo_documento }}: {{ $compra->proveedor->persona->numero_documento }}</small>
            </div>
            <div class="col-md-4">
                <div class="label-title">Sucursal</div>
                <div class="value-text">{{ $compra->almacen->nombre ?? 'N/A' }}</div>
            </div>
            <div class="col-md-4">
                <div class="label-title">Tipo Comprobante</div>
                <div class="value-text">{{ $compra->comprobante->tipo_comprobante ?? 'N/A' }}</div>
            </div>
            
            <div class="col-md-4">
                <div class="label-title">Costo Transporte</div>
                <div class="value-text text-danger fw-bold">{{ number_format($compra->costo_transporte, 2) }}</div>
            </div>
        </div>

        <div class="section-title mt-4">
            <i class="fas fa-tasks"></i> Estados y Acciones
        </div>

        <div class="row g-3 mb-2">
            <div class="col-md-6">
                <div class="label-title mb-2">Estado de Pago</div>
                <div class="dropdown">
                    @php
                        $btnClass = (in_array($compra->estado_pago, ['pendiente', '0', 0])) ? 'btn-outline-danger' : 'btn-outline-success';
                        $statusTxt = (in_array($compra->estado_pago, ['pendiente', '0', 0])) ? 'Pendiente' : 'Pagado';
                    @endphp
                    <button class="btn {{ $btnClass }} btn-sm dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ $statusTxt }}
                    </button>
                    <ul class="dropdown-menu w-100 shadow-sm border-0">
                        <li><a class="dropdown-item change-status-pago" href="#" data-compra-id="{{ $compra->id }}" data-status="pagado">Confirmar Pago</a></li>
                        <li><a class="dropdown-item change-status-pago" href="#" data-compra-id="{{ $compra->id }}" data-status="pendiente">Pendiente</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="label-title mb-2">Estado de Entrega</div>
                <div class="dropdown">
                    @php
                        $entregaText = $compra->estado_entrega == 'entregado' ? 'Entregado' : 'Por Recibir';
                        $entregaBtn = $compra->estado_entrega == 'entregado' ? 'btn-outline-success' : 'btn-outline-warning';
                    @endphp
                    <button class="btn {{ $entregaBtn }} btn-sm dropdown-toggle w-100 text-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ $entregaText }}
                    </button>
                    <ul class="dropdown-menu w-100 shadow-sm border-0">
                        <li><a class="dropdown-item change-status-entrega" href="#" data-compra-id="{{ $compra->id }}" data-status="entregado">Recibido (Entregado)</a></li>
                        <li><a class="dropdown-item change-status-entrega" href="#" data-compra-id="{{ $compra->id }}" data-status="por_entregar">Pendiente (Por Recibir)</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-12">
                <div class="label-title">Nota/Observaciones</div>
                <div class="value-text fst-italic text-muted">
                    {{ $compra->nota_personal ?: 'Sin observaciones' }}
                </div>
            </div>
        </div>
    </div>

    <div class="border-section">
        <div class="section-title">
            <i class="fas fa-box"></i> Detalles de Compra
        </div>
        
        <div class="table-responsive">
            <table class="table table-sm table-hover border-bottom">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th>Producto</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">P. Compra</th>
                        <th class="text-end">P. Venta</th>
                        <th class="text-end">Margen</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($compra->detalles as $index => $detalle)
                        @php
                            $compraVal = $detalle->precio_compra;
                            $ventaVal = $detalle->precio_venta;
                            $margen = 0;
                            if($compraVal > 0) {
                                $margen = (($ventaVal - $compraVal) / $compraVal) * 100;
                            }
                            $margenClass = 'text-success';
                            if($margen < 10) $margenClass = 'text-danger';
                            elseif($margen < 30) $margenClass = 'text-warning';
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $detalle->producto->nombre }}</div>
                                <small class="text-muted">{{ $detalle->producto->codigo }}</small>
                            </td>
                            <td class="text-end">{{ number_format($detalle->cantidad, 2) }}</td>
                            <td class="text-end">{{ number_format($detalle->precio_compra, 2) }}</td>
                            <td class="text-end">{{ number_format($detalle->precio_venta, 2) }}</td>
                            <td class="text-end {{ $margenClass }} fw-bold">{{ number_format($margen, 2) }}%</td>
                            <td class="text-end fw-bold">{{ number_format($detalle->cantidad * $detalle->precio_compra, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <td colspan="6" class="text-end fw-bold">TOTAL:</td>
                        <td class="text-end fw-bold text-primary fs-6">{{ number_format($compra->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <div class="text-end small text-muted">
        Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
        <br>
        Esta compra no afecta contabilidad oficial hasta confirmación.
    </div>
</div>
