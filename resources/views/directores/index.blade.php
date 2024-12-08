@php use App\Models\Director; @endphp

@extends('main')
@include('header')

@section('title', 'Directorio de Directores')

@section('content')
    <div class="directores text-center">
        <h1>Directorio de Directores</h1>

        <form action="{{ route('directores.index') }}" class="search-form" method="get">
            @csrf
            <div class="input-group">
                <input type="text" class="form-control search-input" id="search" name="search" placeholder="Nombre del Director">
                <div class="input-group-append">
                    <button class="btn search-button" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        @if(count($directores) > 0)
            <div class="d-flex flex-wrap justify-content-center mt-4">
                @foreach($directores as $director)
                    <a href="{{ route('directores.show', $director->id) }}" class="text-decoration-none">
                        <div class="director-card">
                            <img
                                src="{{ $director->imagen != Director::$IMAGEN_DEFAULT ? asset('storage/' . $director->imagen) : Director::$IMAGEN_DEFAULT }}"
                                alt="Imagen de {{ $director->nombre }}">
                            <h6>{{ $director->nombre }}</h6>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ningún director registrado.</em></p>
        @endif

        <div class="pagination-container">
            {{ $directores->links('pagination::bootstrap-4') }}
        </div>
    </div>

@endsection

@include('footer')
