@php use App\Models\Genero; @endphp

@extends('main')
@include('header')

@section('title', 'Directorio de Géneros')

@section('content')

    <div class="generos text-center">
        <h1>Directorio de Géneros</h1>

        <form action="{{ route('generos.index') }}" class="search-form" method="get">
            @csrf
            <div class="input-group">
                <input type="text" class="form-control search-input" id="search" name="search" placeholder="Nombre del Genero">
                <div class="input-group-append">
                    <button class="btn search-button" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        @if(count($generos) > 0)
            <div class="generos-container">
                @foreach($generos as $genero)
                    <a href="{{ route('generos.show', $genero->id) }}" class="text-decoration-none">
                        <div class="genero-card">
                            <img
                                src="{{ $genero->imagen != Genero::$IMAGEN_DEFAULT ? asset('storage/' . $genero->imagen) : Genero::$IMAGEN_DEFAULT }}"
                                alt="Imagen de {{ $genero->nombre }}">
                            <h6>{{ $genero->nombre }}</h6>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ningún género registrado.</em></p>
        @endif

        <div class="pagination-container">
            {{ $generos->links('pagination::bootstrap-4') }}
        </div>
    </div>

@endsection

@include('footer')
