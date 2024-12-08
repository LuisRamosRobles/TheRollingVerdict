@php use App\Models\Director; @endphp

@extends('main')
@include('header')

@section('title', 'Directores Eliminados')

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

    <div class="directores">
        <h1>Directores Eliminados</h1>

        @if(count($directores) > 0)
            <div class="row">
                @foreach($directores as $director)
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body">

                                @if($director->imagen != Director::$IMAGEN_DEFAULT)
                                    <img alt="Imagen de {{ $director->nombre }}" class="img-fluid"
                                         src="{{ asset('storage/' . $director->imagen) }}"
                                         width="230px" height="340px">
                                @else
                                    <img alt="Imagen por defecto" class="img-fluid"
                                         src="{{ Director::$IMAGEN_DEFAULT }}">
                                @endif

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
    <a class="btn btn-secondary mx-2 mb-4" href="{{ route('admin.directores') }}">Volver</a>

@endsection
@include('footer')
