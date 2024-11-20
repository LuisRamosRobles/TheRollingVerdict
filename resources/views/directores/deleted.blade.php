@php use App\Models\Director; @endphp

@extends('main')
@include('header')

@section('title', 'Directores Eliminados')

@section('content')
    <div class="directores">
        <h1>Directores Eliminados</h1>

        @if(count($directores) > 0)
            <div class="row">
                @foreach($directores as $director)
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <img alt="Imagen por defecto" class="img-fluid"
                                    src="{{Director::$IMAGEN_DEFAULT}}">

                                <h6 class="card-title">{{$director->nombre}}</h6>

                                <form action="{{ route('directores.restore', $director->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm mt-2">Restaurar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ningún director eliminado.</em></p>
        @endif

        <div class="pagination-container">
            {{ $directores->links('pagination::bootstrap-4') }}
        </div>
    </div>
    <a class="btn btn-secondary mx-2 mb-4" href="{{ route('premios.index') }}">Volver</a>

@endsection
@include('footer')
