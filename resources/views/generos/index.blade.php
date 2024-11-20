@php use App\Models\Genero; @endphp

@extends('main')
@include('header')

@section('title', 'Directorio de Géneros')

@section('content')

    <div class="generos">
        <h1>Directorio Género</h1>

        @if(count($generos) > 0)
            <div class="row ">
                @foreach($generos as $genero)
                    <div class="col-md-3">
                        <a href="{{ route('generos.show', $genero->id) }}" class="text-decoration-none">
                            <div class="card h-100">
                                <div class="card-body">
                                    @if($genero->imagen != Genero::$IMAGEN_DEFAULT)
                                        <img alt="Imagen del Genero" class="img-fluid"
                                             src="{{ asset('storage/' . $genero->imagen) }}"
                                             width="380px" height="220px">
                                    @else
                                        <img alt="Imagen por defecto" class="img-fluid"
                                             src="{{Genero::$IMAGEN_DEFAULT}}">
                                    @endif

                                    <h6 class="card-title card-title-link">{{$genero->nombre}}</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ningún dato.</em></p>
        @endif

        <div class="pagination-container-generos">
            {{$generos->links('pagination::bootstrap-4')}}
        </div>

        <a class="btn btn-success mb-3" href="{{route('generos.create')}}">Añadir Género Nuevo</a>
        <a class="btn btn-info mb-3" href="{{route('generos.deleted')}}">Géneros Eliminados</a>
    </div>


@endsection
@include('footer')
