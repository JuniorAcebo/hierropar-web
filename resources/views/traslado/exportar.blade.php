@extends('layouts.app')

@section('title', 'Exportar Traslados')

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .export-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .export-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .export-card h2 {
            margin-top: 0;
            font-size: 28px;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group-export {
            margin-bottom: 20px;
        }

        .form-group-export label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group-export input[type="date"],
        .form-group-export select {
            width: 100%;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            background: white;
            color: #333;
        }

        .format-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .format-option {
            padding: 15px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
        }

        .format-option:hover {
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }

        .format-option input[type="radio"] {
            display: none;
        }

        .format-option input[type="radio"]:checked + label {
            font-weight: bold;
        }

        .format-option.selected {
            background: rgba(255,255,255,0.2);
            border-color: white;
        }

        .format-label {
            margin: 0;
            cursor: pointer;
            font-size: 18px;
        }

        .format-icon {
            font-size: 32px;
            margin-bottom: 8px;
            display: block;
        }

        .button-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn-export {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-export-submit {
            background: white;
            color: #667eea;
        }

        .btn-export-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        }

        .btn-export-cancel {
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-export-cancel:hover {
            background: rgba(255,255,255,0.3);
        }

        .info-box {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="export-container">
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('traslados.index') }}">Traslados</a></li>
            <li class="breadcrumb-item active">Exportar</li>
        </ol>

        <div class="export-card">
            <h2><i class="fas fa-download me-2"></i>Exportar Traslados</h2>

            <div class="info-box">
                <i class="fas fa-info-circle me-2"></i>
                Selecciona el rango de fechas y el formato de exportación
            </div>

            <form action="" method="POST" id="exportForm" onsubmit="handleSubmit(event)">
                @csrf

                <!-- Rango de Fechas -->
                <div class="form-group-export">
                    <label for="fecha_inicio"><i class="fas fa-calendar me-2"></i>Fecha Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required 
                        value="{{ old('fecha_inicio', now()->subMonth()->format('Y-m-d')) }}">
                    @error('fecha_inicio') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="form-group-export">
                    <label for="fecha_fin"><i class="fas fa-calendar me-2"></i>Fecha Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" required 
                        value="{{ old('fecha_fin', now()->format('Y-m-d')) }}">
                    @error('fecha_fin') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Estado -->
                <div class="form-group-export">
                    <label for="estado"><i class="fas fa-filter me-2"></i>Estado:</label>
                    <select id="estado" name="estado" class="form-control" required>
                        <option value="">Selecciona un estado</option>
                        <option value="todos">Todos</option>
                        <option value="1">Pendientes</option>
                        <option value="2">Completados</option>
                        <option value="3">Cancelados</option>
                    </select>
                    @error('estado') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Formato -->
                <div class="form-group-export">
                    <label><i class="fas fa-file-export me-2"></i>Formato:</label>
                    <div class="format-options">
                        <div class="format-option selected" onclick="selectFormat('excel', this)">
                            <input type="radio" id="formato_excel" name="formato" value="excel" checked required>
                            <label for="formato_excel" class="format-label">
                                <span class="format-icon">📊</span>
                                Excel
                            </label>
                        </div>
                        <div class="format-option" onclick="selectFormat('pdf', this)">
                            <input type="radio" id="formato_pdf" name="formato" value="pdf" required>
                            <label for="formato_pdf" class="format-label">
                                <span class="format-icon">📄</span>
                                PDF
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="button-group">
                    <button type="submit" class="btn-export btn-export-submit">
                        <i class="fas fa-download me-2"></i>Exportar
                    </button>
                    <a href="{{ route('traslados.index') }}" class="btn-export btn-export-cancel">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function selectFormat(format, element) {
        // Remover clase selected de todos
        document.querySelectorAll('.format-option').forEach(el => {
            el.classList.remove('selected');
        });
        // Agregar clase selected al clickeado
        element.classList.add('selected');
        // Marcar el radio button
        document.getElementById('formato_' + format).checked = true;
    }

    function handleSubmit(event) {
        event.preventDefault();

        const fechaInicio = document.getElementById('fecha_inicio').value;
        const fechaFin = document.getElementById('fecha_fin').value;
        const estado = document.getElementById('estado').value;
        const formato = document.querySelector('input[name="formato"]:checked').value;

        // Validar estado
        if (!estado) {
            Swal.fire({
                icon: 'warning',
                title: 'Advertencia',
                text: 'Debes seleccionar un estado',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        // Validar fechas
        if (new Date(fechaInicio) > new Date(fechaFin)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'La fecha de inicio no puede ser posterior a la fecha de fin',
                confirmButtonColor: '#667eea'
            });
            return;
        }

        // Redirigir al endpoint correcto
        const action = formato === 'excel' 
            ? '{{ route("traslados.exportar-excel") }}'
            : '{{ route("traslados.exportar-pdf") }}';

        document.getElementById('exportForm').action = action;
        document.getElementById('exportForm').submit();
    }

    // Mostrar alertas si existen
    @if (session('warning'))
        Swal.fire({
            icon: 'warning',
            title: 'Advertencia',
            text: '{{ session('warning') }}',
            confirmButtonColor: '#667eea'
        });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: `
                <ul style="text-align: left;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
            confirmButtonColor: '#667eea'
        });
    @endif
</script>
@endpush
