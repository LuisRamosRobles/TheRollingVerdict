@extends('main')
@include('header')

@section('title', 'Gestionar Películas')

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

    <h1>Películas</h1>

    <div class="search-container">
        <form action="{{ route('admin.peliculas') }}" class="search-form-admin" method="get">
            @csrf
            <div class="input-group">
                <input type="text" class="form-control search-input-admin" id="search" name="search" placeholder="Titulo de la Película">
                <div class="input-group-append">
                    <button class="btn search-button-admin" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="button-container">
        <a href="{{ route('peliculas.create') }}" class="btn btn-primary mb-3 mr-2">Añadir Nueva Película</a>
        <a class="btn btn-info mb-3 mr-2" href="{{route('peliculas.deleted')}}">Películas Eliminadas</a>
        <a class="btn btn-secondary mb-3" href="{{ route('admin.dashboard') }}">Volver</a>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>Título</th>
            <th>Fecha de Estreno</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach($peliculas as $pelicula)
            <tr>
                <td>{{ $pelicula->titulo }}</td>
                <td>{{ \Carbon\Carbon::parse($pelicula->estreno)->format('d-m-Y') }}</td>
                <td>
                    <a href="{{ route('peliculas.show', $pelicula->id) }}" class="btn btn-light">Ver</a>
                    <a href="{{ route('peliculas.edit', $pelicula->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('peliculas.destroy', $pelicula->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas borrar esta película?')">Eliminar</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@include('footer')
