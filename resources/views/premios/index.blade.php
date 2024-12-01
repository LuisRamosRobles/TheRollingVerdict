@php use App\Models\Premio; @endphp

@extends('main')
@include('header')

@section('title', 'Directorio de Premios')

@section('content')

    <div class="premios">
        <h1>Directorio de Premios</h1>

        @if(count($premios) > 0)
            <div class="row">
                @foreach($premios as $premio)
                    <div class="col-md-3">
                        <a href="{{ route('premios.show', $premio->id) }}" class="text-decoration-none">
                            <div class="card h-100">
                                <div class="card-body">
                                    @if($premio->imagen != Premio::$IMAGEN_DEFAULT)
                                        <img alt="Imagen del Premio" class="img-fluid"
                                             src="{{ asset('storage/' . $premio->imagen) }}"
                                             width="230px" height="340px">
                                    @else
                                        <img alt="Imagen por defecto" class="img-fluid"
                                             src="{{Premio::$IMAGEN_DEFAULT}}">
                                    @endif
                                    <h5 class="card-title card-title-link mt-4">{{ $premio->nombre }}</h5>
                                    <p class="card-text">
                                        <strong>Categoría:</strong> {{ $premio->categoria }}<br>
                                        <strong>Año:</strong> {{ $premio->anio }}<br>
                                        @if($premio->entidad_type === 'App\Models\Pelicula')
                                            <strong>Película:</strong> {{ $premio->entidad->titulo }}
                                        @elseif($premio->entidad_type === 'App\Models\Director')
                                            <strong>Director:</strong> {{ $premio->entidad->nombre }}
                                        @elseif($premio->entidad_type === 'App\Models\Actor')
                                            <strong>Actor:</strong> {{ $premio->entidad->nombre }}
                                        @endif
                                        @if($premio->pelicula)
                                            <p><strong>Película Asociada:</strong> {{ $premio->pelicula->titulo }}</p>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ningún premio registrado.</em></p>
        @endif

        <div class="pagination-container mt-4">
            {{ $premios->links('pagination::bootstrap-4') }}
        </div>

        <a class="btn btn-success mb-3" href="{{route('premios.create')}}">Añadir Premio</a>
        <a class="btn btn-info mb-3" href="{{route('premios.deleted')}}">Premios Eliminados</a>
    </div>

@endsection
@include('footer')
