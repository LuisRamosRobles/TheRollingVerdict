@php use App\Models\Pelicula; @endphp

@extends('main')
@include('header')

@section('title', 'Películas Eliminadas')

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

    <div class="peliculas">
        <h1>Películas Eliminadas</h1>

        @if(count($peliculas) > 0)
            <div class="row">
                @foreach($peliculas as $pelicula)
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">
                                @if($pelicula->imagen != Pelicula::$IMAGEN_DEFAULT)
                                    <img alt="Imagen de {{ $pelicula->titulo }}" class="img-fluid"
                                         src="{{ asset('storage/' . $pelicula->imagen) }}"
                                         width="230px" height="340px">
                                @else
                                    <img alt="Imagen por defecto" class="img-fluid"
                                         src="{{ Pelicula::$IMAGEN_DEFAULT }}">
                                @endif

                                <h6 class="card-title">{{ $pelicula->titulo }}</h6>

                                <form action="{{ route('peliculas.restore', $pelicula->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm mt-2">Restaurar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="lead"><em>No se ha encontrado ninguna película eliminada.</em></p>
        @endif

        <div class="pagination-container-peliculas">
            {{ $peliculas->links('pagination::bootstrap-4') }}
        </div>
    </div>

    <a class="btn btn-secondary mx-2 mb-4 mt-4" href="{{ route('admin.peliculas') }}">Volver</a>

@endsection
@include('footer')
