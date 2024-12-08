@extends('main')
@include('header')

@section('title', 'Gestionar Premios')

@section('content')

    @if(session('success'))
        <br>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <br>
    @endif

    <h1>Premios</h1>

    <div class="search-container">
        <form action="{{ route('admin.premios') }}" class="search-form-admin" method="get">
            @csrf
            <div class="input-group">
                <input type="text" class="form-control search-input-admin" id="search" name="search" placeholder="Nombre del Premio">
                <div class="input-group-append">
                    <button class="btn search-button-admin" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="button-container">
        <a href="{{ route('premios.create') }}" class="btn btn-primary mb-3 mr-2">Añadir Nuevo Premio</a>
        <a class="btn btn-info mb-3 mr-2" href="{{route('premios.deleted')}}">Premios Eliminados</a>
        <a class="btn btn-secondary mb-3" href="{{ route('admin.dashboard') }}">Volver</a>
    </div>



    <table class="table">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Categoria</th>
            <th>Entregado a:</th>
            <th>Año de entrega</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach($premios as $premio)
            <tr>
                <td>{{ $premio->nombre }}</td>
                <td>{{ $premio->categoria }}</td>
                <td>
                    @if($premio->entidad)
                        @if($premio->entidad_type == 'App\Models\Pelicula')
                            {{ $premio->entidad->titulo }}
                        @elseif($premio->entidad_type == 'App\Models\Director')
                            {{ $premio->entidad->nombre }}
                        @elseif($premio->entidad_type == 'App\Models\Actor')
                            {{ $premio->entidad->nombre }}
                        @endif
                    @else
                        No asociado
                    @endif
                </td>
                <td>{{ $premio->anio }}</td>
                <td>
                    <a href="{{ route('premios.show', $premio->id) }}" class="btn btn-light">Ver</a>
                    <a href="{{ route('premios.edit', $premio->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('premios.destroy', $premio->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas borrar este premio?')">Eliminar</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
@include('footer')
