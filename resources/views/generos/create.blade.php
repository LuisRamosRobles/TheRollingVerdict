@extends('main')
@include('header')

@section('title', 'Crear genero')

@section('content')
    <h1>Crear Género</h1>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <br/>
    @endif

    <form action="{{ route('generos.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <div class="form-group">
            <label for="titulo">Nombre:</label>
            <input class="form-control" id="Nombre" name="nombre" type="text" required>
        </div>

        <div class="form-group">
            <label for="imagen">Imagen:</label>
            <input accept="image/*" class="form-control-file" id="imagen" name="imagen" type="file">
            <small class="form-text text-muted">Tipos de archivos compatibles: jpeg,png,jpg,gif,svg</small>
        </div>

        <button class="btn btn-primary" type="submit">Crear</button>
        <a class="btn btn-secondary mx-2" href="{{ route('generos.index') }}">Volver</a>
    </form>

@endsection
@include('footer')
