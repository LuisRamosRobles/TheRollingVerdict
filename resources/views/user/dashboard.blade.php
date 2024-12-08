@extends('main')
@include('header')

@section('title', 'Mi Dashboard')

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

    <h1>Bienvenido, {{auth()->user()->username}}</h1>
    <p>Estas son todas las reseñas que has puesto:</p>

    <div class="reseñas">
        @forelse($resenas as $resena)
            <div class="resena" style="border-bottom: 1px solid #ddd; padding: 15px 0;">
                <p><strong>Película:</strong> {{ $resena->pelicula->titulo }}</p>
                <p><strong>Calificación:</strong>
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $resena->calificacion)
                            <span style="color: gold;">&#9733;</span>
                        @else
                            <span style="color: lightgray;">&#9734;</span>
                        @endif
                    @endfor
                </p>
                @if($resena->comentario !== null)
                    <p><strong>Comentario:</strong> {{ $resena->comentario }}</p>
                @endif
                <form action="{{ route('resenas.destroy', $resena->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"
                            onclick="return confirm('¿Estás seguro de que deseas borrar esta reseña?')">Eliminar</button>
                </form>
            </div>
        @empty
            <p>No tienes ninguna reseña aún. ¡Anímate a compartir tu opinión!</p>
        @endforelse
    </div>
@endsection
@include('footer')
