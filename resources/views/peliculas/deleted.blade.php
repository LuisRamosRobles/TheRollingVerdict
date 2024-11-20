@php use App\Models\Pelicula; @endphp

@extends('main')
@include('header')

@section('title', 'Películas Eliminadas')

@section('content')

    <div class="peliculas">
        <h1>Películas Eliminadas</h1>

        @if(count($peliculas) > 0)
            <div class="row">
                @foreach($peliculas as $pelicula)
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <img alt="Imagen por defecto" class="img-fluid"
                                         src="{{ Pelicula::$IMAGEN_DEFAULT }}">

                                <h6 class="card-title">{{ $pelicula->titulo }}</h6>

                                <!-- Botón para restaurar el género -->
                                <form action="{{ route('peliculas.restore', $pelicula->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm mt-2">Restaurar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ninguna película eliminada.</em></p>
        @endif

        <div class="pagination-container-peliculas">
            {{ $peliculas->links('pagination::bootstrap-4') }}
        </div>
    </div>

    <a class="btn btn-secondary mx-2 mb-4 mt-4" href="{{ route('peliculas.index') }}">Volver</a>

@endsection
@include('footer')
