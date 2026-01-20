@extends('layouts.app')

@section('title', 'Categorías')

@push('css-datatable')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="{{ asset('css/style_Categoria.css') }}">
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
                <a href="{{ route('categorias.create') }}" class="btn btn-primary-modern">
                    <i class="fas fa-plus me-1"></i> Añadir Nueva Categoría
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
                                <th>Editar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categorias as $categoria)
                                <tr>
                                    <td class="fw-semibold">
                                        {{ $categoria->nombre }}
                                    </td>
                                    <td>
                                        @if($categoria->descripcion)
                                        <div class="descripcion-truncada-modern" title="{{ $categoria->descripcion }}">
                                            {{ $categoria->descripcion }}
                                        </div>
                                        @else
                                        <span class="text-muted fst-italic">Sin descripción</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-btns-modern">
                                            @can('editar-categoria')
                                            <a href="{{ route('categorias.edit', $categoria) }}" class="btn-action btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
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