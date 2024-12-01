@php use App\Models\Pelicula;
     use App\Models\Director;
     use App\Models\Actor;
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
            <p><strong>Fecha de Estreno:</strong> {{ Carbon::parse($pelicula->estreno)->format('d-m-Y') }}</p>
            <p><strong>Sinopsis:</strong> {{ $pelicula->sinopsis }}</p>
            <h3>Géneros</h3>
            <ul>
                @foreach ($pelicula->generos as $genero)
                    <li>{{ $genero->nombre }}</li>
                @endforeach
            </ul>

            <h3>Director</h3>
            <div class="col-md-3">
                @if($pelicula->director)
                    <a href="{{ route('directores.show', $pelicula->director->id) }}" class="text-decoration-none">
                        <div class="card h-100 mb-5">
                            <div class="card-body">
                                @if($pelicula->director->imagen != Director::$IMAGEN_DEFAULT)
                                    <img alt="Imagen del Director" class="img-fluid"
                                         src="{{ asset('storage/' . $pelicula->director->imagen) }}"
                                         width="230px" height="340px">
                                @else
                                    <img alt="Imagen por defecto" class="img-fluid"
                                         src="{{ Director::$IMAGEN_DEFAULT }}">
                                @endif

                                <h6 class="card-title card-title-link">{{ $pelicula->director->nombre }}</h6>
                            </div>
                        </div>
                    </a>
                @else
                    <p>No hay un director asociado a esta película.</p>
                @endif
            </div>

            <h3>Reparto</h3>
            <div class="row">
                @if($pelicula->actores->isNotEmpty())
                    @foreach ($pelicula->actores as $actor)
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('actores.show', $actor->id) }}" class="text-decoration-none">
                                <div class="card h-100">
                                    <div class="card-body">
                                        @if($actor->imagen != Actor::$IMAGEN_DEFAULT)
                                            <img alt="Imagen del Actor" class="img-fluid"
                                                 src="{{ asset('storage/' . $actor->imagen) }}"
                                                 width="230px" height="340px">
                                        @else
                                            <img alt="Imagen por defecto" class="img-fluid"
                                                 src="{{ Actor::$IMAGEN_DEFAULT }}">
                                        @endif

                                        <h6 class="card-title card-title-link text-center mt-1">{{ $actor->nombre }}</h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @else
                    <p>No hay actores asociados con esta película.</p>
                @endif
            </div>


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
