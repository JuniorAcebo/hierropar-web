@extends('admin.layouts.app')

@section('title', 'Reporte de Caja por Usuario')

@section('content')
    @include('admin.layouts.partials.alert')

    <div class="container-fluid px-4 py-4">
        <div class="page-header mb-3">
            <div>
                <h1 class="page-title">Reporte de Caja (Sucursal)</h1>
                <div class="text-muted small">Cuánto debe recoger de caja y cuánto vendió por método de pago.</div>
            </div>
        </div>

        <div class="card-clean mb-3">
            <div class="card-header-clean">
                <div class="card-header-title">
                    <i class="fas fa-filter"></i> Filtros
                </div>
            </div>
            <div class="p-3">
                <form method="GET" action="{{ route('ventas.reporte-caja') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Desde</label>
                            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-search me-1"></i> Ver reporte
                            </button>
                            <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
                                Volver a ventas
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-clean">
            <div class="card-header-clean">
                <div class="card-header-title">
                    <i class="fas fa-cash-register"></i> Resumen por usuario
                </div>
            </div>
            <div class="table-responsive p-3">
                <table class="table table-sm table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Usuario</th>
                            <th class="text-end">Debe recoger (Efectivo)</th>
                            <th class="text-end">Efectivo</th>
                            <th class="text-end">QR</th>
                            <th class="text-end">Débito</th>
                            <th class="text-end">Depósito</th>
                            <th class="text-end">Otro</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($report as $row)
                            <tr>
                                <td class="fw-semibold">{{ $row['usuario'] }}</td>
                                <td class="text-end fw-bold">{{ number_format($row['debe_recoger_caja'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['efectivo'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['qr'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['debito'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['deposito'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['otro'], 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($row['total'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No hay datos en el rango seleccionado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th class="text-end">Totales:</th>
                            <th class="text-end fw-bold">{{ number_format($totales['debe_recoger_caja'], 2) }}</th>
                            <th class="text-end">{{ number_format($totales['efectivo'], 2) }}</th>
                            <th class="text-end">{{ number_format($totales['qr'], 2) }}</th>
                            <th class="text-end">{{ number_format($totales['debito'], 2) }}</th>
                            <th class="text-end">{{ number_format($totales['deposito'], 2) }}</th>
                            <th class="text-end">{{ number_format($totales['otro'], 2) }}</th>
                            <th class="text-end fw-bold">{{ number_format($totales['total'], 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

