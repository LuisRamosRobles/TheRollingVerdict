@extends('main')
@include('header')

@section('title', 'Gestionar Actores')

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

    <h1>Actores</h1>

    <div class="search-container">
        <form action="{{ route('admin.actores') }}" class="search-form-admin" method="get">
            @csrf
            <div class="input-group">
                <input type="text" class="form-control search-input-admin" id="search" name="search" placeholder="Nombre del Actor">
                <div class="input-group-append">
                    <button class="btn search-button-admin" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="button-container">
        <a href="{{ route('actores.create') }}" class="btn btn-primary mb-3 mr-2">Añadir Nuevo Actor</a>
        <a class="btn btn-info mb-3 mr-2" href="{{route('actores.deleted')}}">Actores Eliminados</a>
        <a class="btn btn-secondary mb-3" href="{{ route('admin.dashboard') }}">Volver</a>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach($actores as $actor)
            <tr>
                <td>{{ $actor->nombre }}</td>
                <td>
                    <a href="{{ route('actores.show', $actor->id) }}" class="btn btn-light">Ver</a>
                    <a href="{{ route('actores.edit', $actor->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('actores.destroy', $actor->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas borrar este actor?')">Eliminar</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
@include('footer')

