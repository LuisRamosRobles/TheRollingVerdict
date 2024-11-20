@php use App\Models\Genero; @endphp

@extends('main')
@include('header')

@section('title', 'Géneros Eliminados')

@section('content')

    <div class="generos">
        <h1>Géneros Eliminados</h1>

        @if(count($generos) > 0)
            <div class="row">
                @foreach($generos as $genero)
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <img alt="Imagen por defecto" class="img-fluid"
                                         src="{{ Genero::$IMAGEN_DEFAULT }}">

                                <h6 class="card-title">{{ $genero->nombre }}</h6>

                                <!-- Botón para restaurar el género -->
                                <form action="{{ route('generos.restore', $genero->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm mt-2">Restaurar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ningún género eliminado.</em></p>
        @endif

        <div class="pagination-container-generos">
            {{ $generos->links('pagination::bootstrap-4') }}
        </div>
    </div>

    <a class="btn btn-secondary mx-2 mb-4" href="{{ route('generos.index') }}">Volver</a>

@endsection
@include('footer')
