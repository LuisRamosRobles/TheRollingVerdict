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
            {{$peliculas->links('pagination::bootstrap-4')}}
        </div>
    </div>


    @if(auth()->check() && auth()->user()->role === 'ADMIN')
        <a class="btn btn-secondary mb-4 mx-2" href="{{ url()->previous() }}">Volver</a>
    @else
        <a class="btn btn-secondary mb-4 mx-2" href="{{ route('generos.index') }}">Volver</a>
    @endif

@endsection
@include('footer')
