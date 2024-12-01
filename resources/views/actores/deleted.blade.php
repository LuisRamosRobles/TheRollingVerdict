@php use App\Models\Actor; @endphp

@extends('main')
@include('header')

@section('title', 'Actores Eliminados')

@section('content')
    <div class="actores">
        <h1>Actores Eliminados</h1>

        @if(count($actores) > 0)
            <div class="row">
                @foreach($actores as $actor)
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <img alt="Imagen por defecto" class="img-fluid"
                                     src="{{Actor::$IMAGEN_DEFAULT}}">

                                <h6 class="card-title">{{$actor->nombre}}</h6>

                                <form action="{{ route('actores.restore', $actor->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm mt-2">Restaurar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ningún actor eliminado.</em></p>
        @endif

        <div class="pagination-container">
            {{ $actores->links('pagination::bootstrap-4') }}
        </div>
    </div>
    <a class="btn btn-secondary mx-2 mb-4" href="{{ route('actores.index') }}">Volver</a>

@endsection
@include('footer')
