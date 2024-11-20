@php use App\Models\Pelicula; @endphp

@extends('main')
@include('header')

@section('title', $genero->nombre)

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

    <div class="genero-detalle">

        <h1 class="mr-3">{{ $genero->nombre }}</h1>


        @if(count($peliculas) > 0)
            <div class="row">
                @foreach($peliculas as $pelicula)
                    <div class="col-md-3">
                        <a href="{{ route('peliculas.show', $pelicula->id) }}" class="text-decoration-none">
                            <div class="card h-100">
                                <div class="card-body">
                                    @if($pelicula->imagen != Pelicula::$IMAGEN_DEFAULT)
                                        <img alt="Imagen de {{$pelicula->titulo}}" class="img-fluid"
                                             src="{{ asset('storage/' . $pelicula->imagen) }}"
                                             width="380px" height="220px">
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
    </div>

    <a class="btn btn-success mb-4" href="{{route('generos.edit', $genero->id)}}">Actualizar Genero</a>
    <a class="btn btn-secondary mb-4 mx-2" href="{{ route('generos.index') }}">Volver</a>
    <form action="{{ route('generos.destroy', $genero->id) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger mb-4"
                onclick="return confirm('¿Estás seguro de que deseas borrar este genero?')">Borrar
        </button>
    </form>

@endsection
@include('footer')
