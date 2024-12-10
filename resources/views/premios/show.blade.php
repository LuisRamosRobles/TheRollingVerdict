@php use App\Models\Premio; @endphp

@extends('main')
@include('header')

@section('title', $premio->nombre . ' - ' . $premio->categoria . ' (' . $premio->anio. ') ')

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

    <div class="premio-detalle">
        <div class="info">
            <h1>{{ $premio->nombre }}</h1>
            @if($premio->entidad_type == 'App\Models\Pelicula')
                <p><strong>Película:</strong> {{ $premio->entidad->titulo }}</p>
            @elseif($premio->entidad_type == 'App\Models\Director')
                <p><strong>Director:</strong> {{ $premio->entidad->nombre }}</p>
            @elseif($premio->entidad_type == 'App\Models\Actor')
                <p><strong>Actor:</strong> {{ $premio->entidad->nombre }}</p>
            @endif
            <p><strong>Categoria:</strong> {{ $premio->categoria }}</p>
            <p><strong>Año:</strong> {{ $premio->anio }}</p>
            @if(($premio->entidad_type == 'App\Models\Director' && $premio->pelicula) || ($premio->entidad_type == 'App\Models\Actor' && $premio->pelicula))
                <p><strong>Película Asociada:</strong> {{ $premio->pelicula->titulo }}</p>
            @endif
        </div>
        <div class="imagen">
            @if($premio->imagen != Premio::$IMAGEN_DEFAULT)
                <img alt="Imagen de {{ $premio->nombre }}" class="img-fluid"
                     src="{{ asset('storage/' . $premio->imagen) }}"
                     width="230px" height="340px">
            @else
                <img alt="Imagen por defecto" class="img-fluid" src="{{ Premio::$IMAGEN_DEFAULT }}">
            @endif
        </div>
    </div>


    @if(auth()->check() && auth()->user()->role === 'ADMIN')
        <a class="btn btn-secondary mb-4 mx-2" href="{{ url()->previous() }}">Volver</a>
    @else
        <a class="btn btn-secondary mb-4 mx-2" href="{{ route('premios.index') }}">Volver</a>
    @endif


@endsection
@include('footer')
