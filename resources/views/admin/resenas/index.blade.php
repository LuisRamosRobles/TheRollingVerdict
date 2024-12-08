@extends('main')
@include('header')

@section('title', 'Reseñas por Película')

@section('content')
    <h1 class="text-center">Gestionar Reseñas</h1>

    @if(count($peliculas) > 0)
        <div class="reviews-container">
            @foreach($peliculas as $pelicula)
                <div class="reviews-card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $pelicula->titulo }}</h5>
                        <p>{{ $pelicula->resenas_count }} reseñas</p>
                        <a href="{{ route('admin.resenas.show', $pelicula->id) }}" class="btn">Gestionar Reseñas</a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="lead text-center"><em>No se ha encontrado ninguna película con reseñas.</em></p>
    @endif

    <a class="btn btn-secondary mb-3" href="{{ route('admin.dashboard') }}">Volver</a>
@endsection

@include('footer')
