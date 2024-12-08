@extends('main')
@include('header')

@section('title', 'Gestionar Directores')

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

    <h1>Directores</h1>

    <div class="search-container">
        <form action="{{ route('admin.directores') }}" class="search-form-admin" method="get">
            @csrf
            <div class="input-group">
                <input type="text" class="form-control search-input-admin" id="search" name="search" placeholder="Nombre del Director">
                <div class="input-group-append">
                    <button class="btn search-button-admin" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="button-container">
        <a href="{{ route('directores.create') }}" class="btn btn-primary mb-3 mr-2">Añadir Nuevo Director</a>
        <a class="btn btn-info mb-3 mr-2" href="{{route('directores.deleted')}}">Directores Eliminados</a>
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
        @foreach($directores as $director)
            <tr>
                <td>{{ $director->nombre }}</td>
                <td>
                    <a href="{{ route('directores.show', $director->id) }}" class="btn btn-light">Ver</a>
                    <a href="{{ route('directores.edit', $director->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('directores.destroy', $director->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas borrar este director?')">Eliminar</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
@include('footer')
