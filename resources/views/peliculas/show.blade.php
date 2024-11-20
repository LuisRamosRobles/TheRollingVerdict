@php use App\Models\Pelicula;
     use Carbon\Carbon; @endphp

@extends('main')
@include('header')

@section('title', $pelicula->titulo)

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

    <div class="pelicula-detalle">
        <div class="info">
            <h1>{{ $pelicula->titulo }}</h1>
            <p><strong>Director:</strong> {{ $pelicula->director->nombre }}</p>
            <p><strong>Fecha de Estreno:</strong> {{ Carbon::parse($pelicula->estreno)->format('d-m-Y') }}</p>
            <p><strong>Sinopsis:</strong> {{ $pelicula->sinopsis }}</p>
            <p><strong>Reparto:</strong> {{ $pelicula->reparto }}</p>
            <h3>Géneros:</h3>
            <ul>
                @foreach ($pelicula->generos as $genero)
                    <li>{{ $genero->nombre }}</li>
                @endforeach
            </ul>

            <h3>Premios</h3>
            @if ($pelicula->premios->isNotEmpty())
                <ul>
                    @foreach ($pelicula->premios as $premio)
                        <li>
                            <strong>{{ $premio->nombre }}</strong> - {{ $premio->categoria }} ({{ $premio->anio }})
                        </li>
                    @endforeach
                </ul>
            @else
                <p>No hay premios asociados con esta película.</p>
            @endif
        </div>
        <div class="imagen">
            @if($pelicula->imagen != Pelicula::$IMAGEN_DEFAULT)
                <img alt="Imagen de {{ $pelicula->titulo }}" class="img-fluid"
                     src="{{ asset('storage/' . $pelicula->imagen) }}"
                     width="230px" height="340px">
            @else
                <img alt="Imagen por defecto" class="img-fluid" src="{{ Pelicula::$IMAGEN_DEFAULT }}">
            @endif
        </div>
    </div>

    <a class="btn btn-success mb-4" href="{{route('peliculas.edit', $pelicula->id)}}">Actualizar Película</a>
    <a class="btn btn-secondary mb-4 mx-2" href="{{ route('peliculas.index') }}">Volver</a>
    <form action="{{ route('peliculas.destroy', $pelicula->id) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger mb-4"
                onclick="return confirm('¿Estás seguro de que deseas borrar esta película?')">Borrar
        </button>
    </form>

@endsection
@include('footer')
