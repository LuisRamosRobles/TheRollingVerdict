@php use App\Models\Premio; @endphp

@extends('main')
@include('header')

@section('title', 'Premios Eliminados')

@section('content')
    <div class="premios">
        <h1>Premios Eliminados</h1>

        @if(count($premios) > 0)
            <div class="row">
                @foreach($premios as $premio)
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                @if($premio->imagen != Premio::$IMAGEN_DEFAULT)
                                    <img alt="Imagen {{ $premio->nombre }}" class="img-fluid"
                                         src="{{ asset('storage/' . $premio->imagen) }}">
                                @else
                                    <img alt="Imagen por defecto" class="img-fluid"
                                         src="{{Premio::$IMAGEN_DEFAULT}}">
                                @endif

                                <h5 class="card-title">{{$premio->nombre}}</h5>

                                    <p class="card-text">
                                        <strong>Categoría:</strong> {{ $premio->categoria }}<br>
                                        <strong>Año:</strong> {{ $premio->anio }}<br>
                                        @if($premio->entidad_type === 'App\Models\Pelicula')
                                            <strong>Película:</strong> {{ $premio->entidad->titulo }}
                                        @elseif($premio->entidad_type === 'App\Models\Director')
                                            <strong>Director:</strong> {{ $premio->entidad->nombre }}
                                        @endif
                                    </p>

                                <form action="{{ route('premios.restore', $premio->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm mt-2">Restaurar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ningún premio eliminado.</em></p>
        @endif

        <div class="pagination-container">
            {{ $premios->links('pagination::bootstrap-4') }}
        </div>
    </div>

    <a class="btn btn-secondary mx-2 mb-4" href="{{ route('premios.index') }}">Volver</a>

@endsection
@include('footer')
