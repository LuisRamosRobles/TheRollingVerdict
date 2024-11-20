@php use App\Models\Director; @endphp

@extends('main')
@include('header')

@section('title', 'Directorio de Directores')

@section('content')

    <div class="directores">
        <h1>Directorio de Directores</h1>

        @if(count($directores) > 0)
            <div class="row">
                @foreach($directores as $director)
                    <div class="col-md-3">
                        <a href="{{ route('directores.show', $director->id) }}" class="text-decoration-none">
                            <div class="card h-100">
                                <div class="card-body">
                                    @if($director->imagen != Director::$IMAGEN_DEFAULT)
                                        <img alt="Imagen del Director" class="img-fluid"
                                             src="{{ asset('storage/' . $director->imagen) }}"
                                             width="230px" height="340px">
                                    @else
                                        <img alt="Imagen por defecto" class="img-fluid"
                                             src="{{Director::$IMAGEN_DEFAULT}}">
                                    @endif

                                    <h6 class="card-title card-title-link">{{$director->nombre}}</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ningún dato.</em></p>
        @endif

        <div class="pagination-container">
            {{$directores->links('pagination::bootstrap-4')}}
        </div>

        <a class="btn btn-success mb-3" href="{{route('directores.create')}}">Añadir Director</a>
        <a class="btn btn-info mb-3" href="{{route('directores.deleted')}}">Directores Eliminados</a>
    </div>

@endsection
@include('footer')
