@extends('layouts.app')

@section('title','TipoUnidades')

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
    <h1 class="mt-4 text-center">Tipos de Unidades</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Tipos de Unidades</li>
    </ol>

    @can('crear-tipounidad')
    <div class="mb-4">
        <a href="{{route('tipounidades.create')}}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Añadir Nuevo Tipo de Unidad
        </a>
    </div>
    @endcan

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Tabla de tipos de unidades
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="light-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Editar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tipounidades as $item)
                    <tr>
                        <td class="fw-semibold">
                            {{ $item->nombre }}
                        </td>

                        <td>
                            <div class="descripcion-truncada"
                                title="{{ $item->descripcion }}">
                                {{ $item->descripcion }}
                            </div>
                        </td>

                        <td>
                            <div class="action-btns">
                                @can('editar-tipounidad')
                                <a class="action-btn"
                                href="{{ route('tipounidades.edit', $item) }}"
                                title="Editar">
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
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabla = document.querySelector("#datatablesSimple");
            if (tabla) {
                new simpleDatatables.DataTable(tabla, {
                    perPage: 10,
                    perPageSelect: [10, 25, 50, 100, -1], // -1 = todos
                    searchable: true,
                    labels: {
                        placeholder: "Buscar...",
                        perPage: "Mostrar {select} registros",
                        noRows: "No se encontraron resultados",
                        info: "Mostrando {start} a {end} de {rows} registros"
                    }
                });
            }
        });
    </script>
@endpush