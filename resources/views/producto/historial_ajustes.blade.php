@extends('layouts.app')

@section('title', 'Historial de Ajustes')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center" style="color: #2c3e50; font-weight: 700;">Historial de Ajustes de Stock</h1>

    <div class="card mb-4 mt-4">
        <div class="card-header">
            <i class="fas fa-history me-1"></i>
            Registros de Modificaciones
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <!-- Tabla de historial de ajustes pero al presionar una tabla te un modal -->
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Usuario</th>
                            <th>Producto</th>
                            <th>Almacén</th>
                            <th class="text-center">Cant. Anterior</th>
                            <th class="text-center">Cant. Nueva</th>
                            <!-- Columna para mostrar la cantidad de ajuste-->
                            <th class="text-center">Cant. Ajuste</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ajustes as $ajuste)
                            <tr>
                                <td>{{ $ajuste->created_at->format('d/m/Y H:i') }}</td>
                                <td><span class="badge bg-info text-dark">{{ $ajuste->user->name }}</span></td>
                                <td><strong>{{ $ajuste->producto->nombre }}</strong></td>
                                <td>{{ $ajuste->almacen->nombre }}</td>
                                <td class="text-center">{{ number_format($ajuste->cantidad_anterior, 2) }}</td>
                                <td class="text-center">
                                    <span class="fw-bold {{ $ajuste->cantidad_nueva > $ajuste->cantidad_anterior ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($ajuste->cantidad_nueva, 2) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $ajuste_cantidad = $ajuste->cantidad_nueva - $ajuste->cantidad_anterior;
                                    @endphp
                                    <span class="fw-bold {{ $ajuste_cantidad > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $ajuste_cantidad > 0 ? '+' : '' }}{{ number_format($ajuste_cantidad, 2) }}
                                    </span>
                                </td>
                                <td>{{ $ajuste->motivo }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay registros de ajustes.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <!--detalle modal de usuario producto surcursal-->
                    <div>

                    </div>
                </table>
            </div>
            <div class="mt-3">
                {{ $ajustes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
