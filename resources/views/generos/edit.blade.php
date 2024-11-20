@php use App\Models\Genero; @endphp

@extends('main')
@include('header')

@section('title', 'Actualizar Género')

@section('content')
    <h1>Actualizar Género</h1>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <br/>
    @endif

    <form action="{{ route('generos.update', $genero->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input class="form-control" id="nombre" name="nombre" type="text" required
                   value="{{$genero->nombre}}">
        </div>

        <div class="form-group imagen">
            <label for="imagen">Imagen:</label>
            @if($genero->imagen != Genero::$IMAGEN_DEFAULT)
                <img alt="Imagen de {{ $genero->titulo }}" class="img-fluid" src="{{asset('storage/' . $genero->imagen)}}"
                     width="380px" height="220px">
            @else
                <img alt="Imagen de la Película" class="img-fluid" src="{{ genero::$IMAGEN_DEFAULT }}">
            @endif
            <br>
            <br>
            <input accept="image/*" class="form-control-file" id="imagen" name="imagen" type="file">
            <small class="form-text text-muted">Tipos de archivos compatibles: jpeg,png,jpg,gif,svg</small>
        </div>

        <button class="btn btn-primary" type="submit">Actualizar</button>
        <a class="btn btn-secondary mx-2" href="{{ route('generos.show', $genero->id) }}">Volver</a>
    </form>

@endsection
@include('footer')
