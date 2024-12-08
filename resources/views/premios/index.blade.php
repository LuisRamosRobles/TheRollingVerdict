@php use App\Models\Premio; @endphp

@extends('main')
@include('header')

@section('title', 'Directorio de Premios')

@section('content')

    <div class="premios text-center">
        <h1>Directorio de Premios</h1>

        <form action="{{ route('premios.index') }}" class="search-form" method="get">
            @csrf
            <div class="input-group">
                <input type="text" class="form-control search-input" id="search" name="search" placeholder="Nombre del Premio">
                <div class="input-group-append">
                    <button class="btn search-button" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        @if(count($premios) > 0)
            <div class="d-flex flex-wrap justify-content-center mt-4">
                @foreach($premios as $premio)
                    <a href="{{ route('premios.show', $premio->id) }}" class="text-decoration-none">
                        <div class="premio-card">
                            <img
                                src="{{ $premio->imagen != Premio::$IMAGEN_DEFAULT ? asset('storage/' . $premio->imagen) : Premio::$IMAGEN_DEFAULT }}"
                                alt="Imagen de {{ $premio->nombre }}">
                            <div class="hover-content">
                                <h5 class="hover-title">{{ $premio->nombre }}</h5>
                                <p class="hover-details">
                                    <strong>Categoría:</strong> {{ $premio->categoria }}<br>
                                    <strong>Año:</strong> {{ $premio->anio }}<br>
                                    @if($premio->entidad_type === 'App\Models\Pelicula')
                                        <strong>Película:</strong> {{ $premio->entidad->titulo }}
                                    @elseif($premio->entidad_type === 'App\Models\Director')
                                        <strong>Director:</strong> {{ $premio->entidad->nombre }}
                                    @elseif($premio->entidad_type === 'App\Models\Actor')
                                        <strong>Actor:</strong> {{ $premio->entidad->nombre }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ningún premio registrado.</em></p>
        @endif

        <div class="pagination-container mt-4">
            {{ $premios->links('pagination::bootstrap-4') }}
        </div>
    </div>


@endsection
@include('footer')
