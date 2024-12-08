@extends('main')
@include('header')

@section('title', 'Añadir Película')

@section('content')
    <h1>Añadir Película</h1>

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

    <form action="{{ route('peliculas.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <div class="form-group">
            <label for="titulo">Título:</label>
            <input class="form-control" id="titulo" name="titulo" type="text" required>
        </div>

        <div class="form-group">
            <label for="estreno">Fecha de Estreno (Formato: AAAA-MM-DD):</label>
            <input class="form-control" id="estreno" name="estreno" type="text" placeholder="AAAA-MM-DD" required>
        </div>


        <div class="form-group">
            <label for="director_id">Director:</label>
            <select class="form-control" id="director_id" name="director_id" required>
                <option value="">Seleccione un director</option>
                @foreach($directores as $director)
                    <option value="{{ $director->id }}">{{ $director->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="sinopsis">Sinopsis:</label>
            <textarea class="form-control" id="sinopsis" name="sinopsis" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label for="reparto"><h4>Reparto:</h4></label>
            <div class="row">
                <div class="col-md-6">
                    <label for="actores-disponibles">Actores Disponibles:</label>
                    <select id="actores-disponibles" class="form-control" size="10" multiple>
                        @foreach($actores as $actor)
                            <option value="{{ $actor->id }}">{{ $actor->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="reparto-seleccionado">Reparto Seleccionado:</label>
                    <select id="reparto-seleccionado" class="form-control" size="10" name="reparto[]" multiple>

                    </select>
                </div>
            </div>
            <div class="mt-2">
                <button type="button" class="btn btn-primary" id="agregar-actor">Agregar &gt;&gt;</button>
                <button type="button" class="btn btn-secondary" id="remover-actor">&lt;&lt; Remover</button>
            </div>
        </div>


        <div class="form-group">
            <label for="generos">Géneros:</label>
            @foreach($generos as $genero)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="generos[]" value="{{ $genero->id }}" id="genero{{ $genero->id }}">
                    <label class="form-check-label" for="genero{{ $genero->id }}">{{ $genero->nombre }}</label>
                </div>
            @endforeach
        </div>

        <div class="form-group">
            <label for="imagen">Imagen:</label>
            <input accept="image/*" class="form-control-file" id="imagen" name="imagen" type="file">
            <small class="form-text text-muted">Tipos de archivos compatibles: jpeg,png,jpg,gif,svg</small>
        </div>

        <button class="btn btn-primary" type="submit">Crear</button>
        <a class="btn btn-secondary mx-2" href="{{ route('admin.peliculas') }}">Volver</a>
    </form>

    <script>
        let premioCount = 0;

        document.addEventListener('DOMContentLoaded', function () {
            const actoresDisponibles = document.getElementById('actores-disponibles');
            const repartoSeleccionado = document.getElementById('reparto-seleccionado');
            const agregarActorBtn = document.getElementById('agregar-actor');
            const removerActorBtn = document.getElementById('remover-actor');


            agregarActorBtn.addEventListener('click', function () {
                Array.from(actoresDisponibles.selectedOptions).forEach(option => {
                    repartoSeleccionado.appendChild(option);
                });
            });


            removerActorBtn.addEventListener('click', function () {
                Array.from(repartoSeleccionado.selectedOptions).forEach(option => {
                    actoresDisponibles.appendChild(option);
                });
            });
        });

    </script>



@endsection
@include('footer')
