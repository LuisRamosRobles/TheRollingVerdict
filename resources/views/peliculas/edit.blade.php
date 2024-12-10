@php use App\Models\Pelicula; @endphp

@extends('main')
@include('header')

@section('title', 'Actualizar Info Película')

@section('content')

    <h1>Actualizar Película</h1>

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

    <form id="form-actualizar-pelicula" action="{{ route('peliculas.update', $pelicula->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="form-group">
            <label for="titulo">Título:</label>
            <input class="form-control" id="titulo" name="titulo" type="text" required
                   value="{{$pelicula->titulo}}">
        </div>

        <div class="form-group">
            <label for="estreno">Fecha de Estreno (Formato: AAAA-MM-DD):</label>
            <input class="form-control" id="estreno" name="estreno" type="text" placeholder="AAAA-MM-DD" required
                   value="{{$pelicula->estreno}}">
        </div>


        <div class="form-group">
            <label for="director">Director:</label>
            <select class="form-control" id="director_id" name="director_id" required>
                @foreach($directores as $director)
                    <option value="{{ $director->id }}" {{ $pelicula->director_id == $director->id ? 'selected' : '' }}>
                        {{ $director->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="sinopsis">Sinopsis:</label>
            <textarea class="form-control" id="sinopsis" name="sinopsis" rows="3" required>{{ old('sinopsis', $pelicula->sinopsis) }}</textarea>
        </div>

        <div class="form-group">
            <label for="reparto"><h4>Reparto:</h4></label>
            <div class="row">
                <div class="col-md-6">
                    <label for="actores-disponibles">Actores Disponibles:</label>
                    <select id="actores-disponibles" class="form-control" size="10" multiple>
                        @foreach($actoresDisponibles as $actor)
                            <option value="{{ $actor->id }}">{{ $actor->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="reparto-seleccionado">Reparto Seleccionado:</label>
                    <select id="reparto-seleccionado" class="form-control" size="10" name="reparto[]" multiple>
                        @foreach($repartoSeleccionado as $actor)
                            <option value="{{ $actor->id }}" onload="agregarAlReparto({{ $actor->id }})">
                                {{ $actor->nombre }}
                            </option>
                        @endforeach
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
                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="generos[]"
                        value="{{ $genero->id }}"
                        id="genero{{ $genero->id }}"
                        {{ $pelicula->generos->contains($genero->id) ? 'checked' : '' }}>
                    <label class="form-check-label" for="genero{{ $genero->id }}">{{ $genero->nombre }}</label>
                </div>
            @endforeach
        </div>

        <div class="form-group imagen">
            <label for="imagen">Imagen:</label>
            @if($pelicula->imagen != Pelicula::$IMAGEN_DEFAULT)
                <img alt="Imagen de {{ $pelicula->titulo }}" class="img-fluid" src="{{asset('storage/' . $pelicula->imagen)}}"
                     width="230px" height="340px" >
            @else
                <img alt="Imagen por defecto" class="img-fluid" src="{{ Pelicula::$IMAGEN_DEFAULT }}">
            @endif
            <br>
            <br>
            <input accept="image/*" class="form-control-file" id="imagen" name="imagen" type="file">
            <small class="form-text text-muted">Tipos de archivos compatibles: jpeg,png,jpg,gif,svg</small>
        </div>

        <button class="btn btn-primary" type="submit">Actualizar</button>
        <a class="btn btn-secondary mx-2" href="{{ route('admin.peliculas') }}">Volver</a>
    </form>

    <script>
        let repartoArray = [];


        function agregarAlReparto(id) {
            if (!repartoArray.includes(id)) {
                repartoArray.push(id);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const actoresDisponibles = document.getElementById('actores-disponibles');
            const repartoSeleccionado = document.getElementById('reparto-seleccionado');
            const agregarActorBtn = document.getElementById('agregar-actor');
            const removerActorBtn = document.getElementById('remover-actor');




            Array.from(repartoSeleccionado.options).forEach(option => {
                agregarAlReparto(option.value);
            });

            console.log('Array inicializado:', repartoArray);


            agregarActorBtn.addEventListener('click', function () {
                Array.from(actoresDisponibles.selectedOptions).forEach(option => {
                    repartoArray.push(option.value);
                    repartoSeleccionado.appendChild(option);
                });
            });


            removerActorBtn.addEventListener('click', function () {
                Array.from(repartoSeleccionado.selectedOptions).forEach(option => {
                    repartoArray = repartoArray.filter(id => id !== option.value);
                    actoresDisponibles.appendChild(option);
                });
            });
        });

        document.getElementById('form-actualizar-pelicula').addEventListener('submit', function () {
            const repartoSeleccionado = document.getElementById('reparto-seleccionado');
            repartoSeleccionado.innerHTML = '';

            repartoArray.forEach(id => {
                const option = document.createElement('option');
                option.value = id;
                option.selected = true;
                repartoSeleccionado.appendChild(option);
            });
        });
    </script>



@endsection
@include('footer')
