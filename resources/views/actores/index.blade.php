@php use App\Models\Actor; @endphp

@extends('main')
@include('header')

@section('title', 'Directorio de Actores')

@section('content')
    <div class="actores">
        <h1>Directorio de Actores</h1>

        @if(count($actores) > 0)
            <div class="row">
                @foreach($actores as $actor)
                    <div class="col-md-3">
                        <a href="{{ route('actores.show', $actor->id) }}" class="text-decoration-none">
                            <div class="card h-100">
                                <div class="card-body">
                                    @if($actor->imagen!= Actor::$IMAGEN_DEFAULT)
                                        <img alt="Imagen del Actor" class="img-fluid"
                                             src="{{ asset('storage/'. $actor->imagen) }}"
                                             width="230px" height="340px">
                                    @else
                                        <img alt="Imagen por defecto" class="img-fluid"
                                             src="{{ Actor::$IMAGEN_DEFAULT }}"
                                             width="230px" height="340px">
                                    @endif
                                    <h6 class="card-title mt-2">{{$actor->nombre}}</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ningún actor registrado.</em></p>
        @endif

        <div class="pagination-container">
            {{$actores->links('pagination::bootstrap-4')}}
        </div>

        <a class="btn btn-success mb-3" href="{{ route('actores.create') }}">Añadir Actor</a>
        <a class="btn btn-info mb-3" href="{{ route('actores.deleted')}}">Actores Eliminados</a>
    </div>

@endsection
@include('footer')
