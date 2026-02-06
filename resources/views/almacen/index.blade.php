@extends('layouts.app')

@section('title','Almacenes')

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
    <h1 class="mt-4 text-center">Almacenes</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Almacenes</li>
    </ol>

    @can('crear-almacen')
    <div class="mb-4">
        <a href="{{route('almacenes.create')}}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Añadir Nuevo Almacen
        </a>
    </div>
    @endcan

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Tabla de Almacenes
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="light-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Direccion</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($almacenes as $item)
                    <tr>
                        <td class="fw-semibold">
                            {{ $item->codigo }}
                        </td>

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
                            <div class="descripcion-truncada"
                                title="{{ $item->direccion }}">
                                {{ $item->direccion }}
                            </div>
                        </td>

                       <td>
                            @if ($item->estado)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-danger">Inactivo</span>
                            @endif
                        </td>

                        <td>

                            
                            <div class="action-btns">

                                @can('dar-de-baja-almacen')
                                <form action="{{ route('almacenes.updateEstado', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="action-btn"
                                        title="{{ $item->estado ? 'Dar de baja' : 'Activar' }}"
                                        onclick="return confirm('¿Seguro que deseas cambiar el estado del almacén?')">
                                        @if($item->estado == '1')
                                            <!-- Si está activo, mostramos ícono de basura para dar de baja -->
                                            <i class="fas fa-trash text-danger"></i>
                                        @else
                                            <!-- Si está inactivo, mostramos ícono de check para activar -->
                                            <i class="fas fa-check text-success"></i>
                                        @endif
                                    </button>
                                </form>
                                @endcan
                                
                                
                                @can('editar-almacen')
                                <a class="action-btn"
                                href="{{ route('almacenes.edit', $item->id) }}"
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