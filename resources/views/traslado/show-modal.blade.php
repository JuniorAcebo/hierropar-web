<div class="modal-header">
    <h5 class="modal-title">Detalles del Traslado #{{ $traslado->id }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="row mb-3">
        <div class="col-md-6">
            <strong>Origen:</strong>
            <p>{{ $traslado->origen }}</p>
        </div>
        <div class="col-md-6">
            <strong>Destino:</strong>
            <p>{{ $traslado->destino }}</p>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <strong>Fecha y Hora:</strong>
            <p>{{ $traslado->fecha_hora->format('d/m/Y H:i') }}</p>
        </div>
        <div class="col-md-6">
            <strong>Costo de Envío:</strong>
            <p>${{ number_format($traslado->costo_envio, 2) }}</p>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <strong>Usuario:</strong>
            <p>{{ $traslado->user->name }}</p>
        </div>
        <div class="col-md-6">
            <strong>Estado:</strong>
            <p>
                @if ($traslado->estado == 1)
                    <span class="badge bg-success">Activo</span>
                @else
                    <span class="badge bg-danger">Inactivo</span>
                @endif
            </p>
        </div>
    </div>

    <hr>

    <h6 class="mb-3"><i class="fas fa-boxes"></i> Productos del Traslado</h6>
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($traslado->detalles as $detalle)
                    <tr>
                        <td>
                            <strong>{{ $detalle->producto->nombre }}</strong>
                            <br>
                            <small class="text-muted">{{ $detalle->producto->codigo }}</small>
                        </td>
                        <td>{{ number_format($detalle->cantidad, 4) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
    @can('editar-traslado')
        <a href="{{ route('traslados.edit', $traslado->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Editar
        </a>
    @endcan
</div>
