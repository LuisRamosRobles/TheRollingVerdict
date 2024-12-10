@php use App\Models\Genero; @endphp

@extends('main')
@include('header')

@section('title', 'Géneros Eliminados')

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

    <div class="generos">
        <h1>Géneros Eliminados</h1>

        @if(count($generos) > 0)
            <div class="row">
                @foreach($generos as $genero)
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">

                                @if($genero->imagen != Genero::$IMAGEN_DEFAULT)
                                    <img alt="Imagen de {{ $genero->nombre }}" class="img-fluid"
                                         src="{{ asset('storage/' . $genero->imagen) }}"
                                         width="230px" height="340px">
                                @else
                                    <img alt="Imagen por defecto" class="img-fluid"
                                         src="{{ Genero::$IMAGEN_DEFAULT }}">
                                @endif

                                <h6 class="card-title">{{ $genero->nombre }}</h6>

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

    <a class="btn btn-secondary mx-2 mb-4 mt-5" href="{{ route('admin.generos') }}">Volver</a>

@endsection
@include('footer')
