@php use App\Models\Actor; @endphp

@extends('main')
@include('header')

@section('title', 'Actores Eliminados')

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


    <div class="actores">
        <h1>Actores Eliminados</h1>

        @if(count($actores) > 0)
            <div class="row">
                @foreach($actores as $actor)
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                @if($actor->imagen != Actor::$IMAGEN_DEFAULT)
                                    <img alt="Imagen de {{ $actor->nombre }}" class="img-fluid"
                                         src="{{ asset('storage/' . $actor->imagen) }}"
                                         width="230px" height="340px">
                                @else
                                    <img alt="Imagen por defecto" class="img-fluid"
                                         src="{{ Actor::$IMAGEN_DEFAULT }}">
                                @endif

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
    <a class="btn btn-secondary mx-2 mb-4" href="{{ route('admin.actores') }}">Volver</a>

@endsection
@include('footer')
