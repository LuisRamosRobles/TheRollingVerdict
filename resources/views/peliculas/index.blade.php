@php use App\Models\Pelicula; @endphp

@extends('main')
@include('header')

@section('title', 'Biblioteca de películas')

@section('content')

    <div class="peliculas">
        <h1>Biblioteca de películas</h1>

        @if(count($peliculas) > 0)
            <div class="row">
                @foreach($peliculas as $pelicula)
                    <div class="col-md-3">
                        <a href="{{ route('peliculas.show', $pelicula->id) }}" class="text-decoration-none">
                            <div class="card h-100">
                                <div class="card-body">
                                    @if($pelicula->imagen != Pelicula::$IMAGEN_DEFAULT)
                                        <img alt="Imagen de la Pelicula" class="img-fluid"
                                             src="{{ asset('storage/' . $pelicula->imagen) }}"
                                             width="230px" height="340px">
                                    @else
                                        <img alt="Imagen por defecto" class="img-fluid"
                                             src="{{Pelicula::$IMAGEN_DEFAULT}}">
                                    @endif

                                    <h6 class="card-title card-title-link">{{$pelicula->titulo}}</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ningún dato.</em></p>
        @endif

        <div class="pagination-container">
            {{$peliculas->links('pagination::bootstrap-4')}}
        </div>

        <a class="btn btn-success mb-3" href="{{route('peliculas.create')}}">Añadir Película</a>
        <a class="btn btn-info mb-3" href="{{route('peliculas.deleted')}}">Películas Eliminadas</a>
    </div>


@endsection
@include('footer')
