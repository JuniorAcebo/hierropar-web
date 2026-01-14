@extends('layouts.app')

@section('title', 'Crear categoría')

@push('css')
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --light-color: #ecf0f1;
            --text-color: #2c3e50;
        }

        .form-container {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(44, 62, 80, 0.1);
            border: 1px solid #e9ecef;
            max-width: 600px;
            margin: 0 auto;
        }

        .form-label {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            background-color: #ffffff;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            color: var(--text-color);
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            background-color: #ffffff;
        }

        #descripcion {
            resize: none;
            min-height: 100px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-color) 0%, #2980b9 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
            transition: all 0.3s ease;
            min-width: 120px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }

        small.text-danger {
            color: #e74c3c !important;
            font-size: 0.8rem;
            font-weight: 500;
            display: block;
            margin-top: 0.25rem;
        }

        .breadcrumb {
            background-color: transparent;
            padding: 0.5rem 0;
            margin-bottom: 1.5rem;
        }

        .breadcrumb-item a {
            color: #7f8c8d;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb-item a:hover {
            color: var(--accent-color);
        }

        h1 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }

        .card {
            border: none;
            background: transparent;
        }

        .card-footer {
            background: transparent;
            border-top: 1px solid #e9ecef;
            padding: 1.5rem 0 0 0;
            margin-top: 1rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
                margin: 0 1rem;
            }

            h1 {
                font-size: 1.5rem;
            }

            .btn-primary {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-3 px-md-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Crear Categoría</h1>
            <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Volver
            </a>
        </div>

        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('categorias.index') }}">Categorías</a></li>
                <li class="breadcrumb-item active">Crear categoría</li>
            </ol>
        </nav>

        <div class="form-container">
            <form action="{{ route('categorias.store') }}" method="post">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label for="nombre" class="form-label">Nombre de la categoría:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre') }}"
                            placeholder="Ingrese el nombre de la categoría">
                        @error('nombre')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="form-control"
                            placeholder="Describa la categoría (opcional)">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Guardar Categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // Efecto de foco suave en los inputs
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control');

            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.transform = 'translateY(-1px)';
                });

                input.addEventListener('blur', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
@endpush
