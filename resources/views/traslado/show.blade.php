@extends('layouts.app')

@section('title', 'Ver Traslado')

@push('css')
    <style>
        .info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .info-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            border-left: 4px solid #667eea;
        }

        .info-item strong {
            color: #667eea;
            display: block;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .info-item p {
            margin: 0;
            font-size: 1.1rem;
            color: #2c3e50;
        }

        .table-responsive {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem 0.8rem;
            border: none;
        }

        .table td {
            padding: 0.8rem;
            vertical-align: middle;
            border-bottom: 1px solid #ecf0f1;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            padding: 0.5rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .btn {
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c3e50;
            margin-top: 2rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid #667eea;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: #667eea;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mt-4 mb-4">
            <h1>Traslado #{{ $traslado->id }}</h1>
            <div>
                @can('editar-traslado')
                    <a href="{{ route('traslados.edit', $traslado->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                @endcan
                <a href="{{ route('traslados.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('traslados.index') }}">Traslados</a></li>
            <li class="breadcrumb-item active">Traslado #{{ $traslado->id }}</li>
        </ol>

        <!-- Tarjeta de información principal -->
        <div class="info-card">
            <h4 class="mb-0">
                <i class="fas fa-exchange-alt"></i> Información del Traslado
            </h4>
        </div>

        <!-- Información general -->
        <div class="info-row">
            <div class="info-item">
                <strong>Almacén Origen</strong>
                <p>{{ $traslado->origenAlmacen?->nombre ?? 'N/A' }}</p>
            </div>
            <div class="info-item">
                <strong>Almacén Destino</strong>
                <p>{{ $traslado->destinoAlmacen?->nombre ?? 'N/A' }}</p>
            </div>
            <div class="info-item">
                <strong>Fecha y Hora</strong>
                <p>{{ $traslado->fecha_hora->format('d/m/Y H:i') }}</p>
            </div>
            <div class="info-item">
                <strong>Costo de Envío</strong>
                <p>${{ number_format($traslado->costo_envio, 2) }}</p>
            </div>
            <div class="info-item">
                <strong>Usuario</strong>
                <p>{{ $traslado->user->name }}</p>
            </div>
            <div class="info-item">
                <strong>Estado</strong>
                <p>
                    @if ($traslado->estado == 1)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-danger">Inactivo</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Productos del traslado -->
        <div class="section-title">
            <i class="fas fa-boxes"></i> Productos del Traslado
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="40%">Producto</th>
                        <th width="15%">Código</th>
                        <th width="20%">Cantidad</th>
                        <th width="20%">Categoría</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($traslado->detalles as $index => $detalle)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $detalle->producto->nombre }}</strong>
                                <br>
                                <small class="text-muted">{{ $detalle->producto->descripcion }}</small>
                            </td>
                            <td><code>{{ $detalle->producto->codigo }}</code></td>
                            <td>{{ number_format($detalle->cantidad, 4) }} {{ $detalle->producto->tipounidad->nombre ?? 'U' }}</td>
                            <td><span class="badge bg-info">{{ $detalle->producto->categoria->nombre ?? 'N/A' }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Resumen -->
        <div class="mt-4">
            <div class="info-row">
                <div class="info-item">
                    <strong>Total de Productos</strong>
                    <p>{{ $traslado->detalles->count() }}</p>
                </div>
                <div class="info-item">
                    <strong>Cantidad Total Trasladada</strong>
                    <p>{{ number_format($traslado->detalles->sum('cantidad'), 4) }}</p>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="mt-4 mb-4">
            @can('editar-traslado')
                <a href="{{ route('traslados.edit', $traslado->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar Traslado
                </a>
            @endcan
            @can('eliminar-traslado')
                <button class="btn btn-danger" onclick="eliminarTraslado({{ $traslado->id }})">
                    <i class="fas fa-trash"></i> Eliminar Traslado
                </button>
            @endcan
            <a href="{{ route('traslados.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Traslados
            </a>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function eliminarTraslado(id) {
            Swal.fire({
                title: '¿Está seguro?',
                text: 'El traslado se eliminará permanentemente y se revertirá el stock',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/traslados/${id}`;
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush
