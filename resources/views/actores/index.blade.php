@php use App\Models\Actor; @endphp

@extends('main')
@include('header')

@section('title', 'Directorio de Actores')

@section('content')
    <div class="actores text-center">
        <h1>Directorio de Actores</h1>


        <form action="{{ route('actores.index') }}" class="search-form" method="get">
            @csrf
            <div class="input-group">
                <input type="text" class="form-control search-input" id="search" name="search" placeholder="Nombre del Actor">
                <div class="input-group-append">
                    <button class="btn search-button" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        @if(count($actores) > 0)
            <div class="d-flex flex-wrap justify-content-center mt-4">
                @foreach($actores as $actor)
                    <a href="{{ route('actores.show', $actor->id) }}" class="text-decoration-none">
                        <div class="actor-card">
                            <img
                                src="{{ $actor->imagen != Actor::$IMAGEN_DEFAULT ? asset('storage/' . $actor->imagen) : Actor::$IMAGEN_DEFAULT }}"
                                alt="Imagen de {{ $actor->nombre }}">
                            <h6>{{ $actor->nombre }}</h6>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ningún actor registrado.</em></p>
        @endif

        <div class="pagination-container">
            {{ $actores->links('pagination::bootstrap-4') }}
        </div>
    </div>

@endsection

@include('footer')
