@php use App\Models\Pelicula; @endphp

@extends('main')
@include('header')

@section('title', 'Biblioteca de películas')

@section('content')

    <div class="peliculas text-center">
        <h1>Biblioteca de películas</h1>

        <form action="{{ route('peliculas.index') }}" class="search-form" method="get">
            @csrf
            <div class="input-group">
                <input type="text" class="form-control search-input" id="search" name="search" placeholder="Titulo de la Película">
                <div class="input-group-append">
                    <button class="btn search-button" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        @if(count($peliculas) > 0)
            <div class="d-flex flex-wrap justify-content-center mt-4">
                @foreach($peliculas as $pelicula)
                    <a href="{{ route('peliculas.show', $pelicula->id) }}" class="text-decoration-none">
                        <div class="pelicula-card">
                            <img
                                src="{{ $pelicula->imagen != Pelicula::$IMAGEN_DEFAULT ? asset('storage/' . $pelicula->imagen) : Pelicula::$IMAGEN_DEFAULT }}"
                                alt="Imagen de {{ $pelicula->titulo }}">

                            <div class="hover-content">
                                <div class="nota-media">{{ number_format($pelicula->promedio_calificacion, 1) }}</div>
                                <div class="rating">

                                    @for($i = 5; $i >= 1; $i--)
                                        @if($i <= floor($pelicula->promedio_calificacion))
                                            <span>&#9733;</span>
                                        @elseif($i == ceil($pelicula->promedio_calificacion))
                                            <span>&#9734;</span>
                                        @else
                                            <span class="empty">&#9733;</span>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ninguna película registrada.</em></p>
        @endif

        <div class="pagination-container">
            {{ $peliculas->links('pagination::bootstrap-4') }}
        </div>
    </div>

@endsection

@include('footer')
