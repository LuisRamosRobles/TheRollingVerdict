@extends('main')
@include('header')

@section('title', 'Actualizar Premio')

@section('content')
    <h1>Actualizar Premio</h1>

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

    <form action="{{ route('premios.update', $premio->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="form-group">
            <label for="entidad-titulo">¿Para quién es el Premio?</label><br>
            <input type="radio" id="pelicula" name="entidad_type" value="App\Models\Pelicula"
                {{ $premio->entidad_type == 'App\Models\Pelicula' ? 'checked' : '' }}>
            <label for="pelicula">Película</label>

            <input type="radio" id="director" name="entidad_type" value="App\Models\Director"
                {{ $premio->entidad_type == 'App\Models\Director' ? 'checked' : '' }}>
            <label for="director">Director</label>

            <input type="radio" id="actor" name="entidad_type" value="App\Models\Actor"
                {{ $premio->entidad_type == 'App\Models\Actor' ? 'checked' : '' }}>
            <label for="actor">Actor</label>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre del Premio:</label>
            <input class="form-control" id="nombre" name="nombre" type="text"
                   value="{{ old('nombre', $premio->nombre) }}" placeholder="Ejemplo: Oscar" required>
        </div>

        <div class="form-group">
            <label for="categoria">Categoría:</label>
            <input class="form-control" id="categoria" name="categoria" type="text"
                   value="{{ old('categoria', $premio->categoria) }}" placeholder="Ejemplo: Mejor Película" required>
        </div>

        <div class="form-group">
            <label for="anio">Año:</label>
            <input class="form-control" id="anio" name="anio" type="number"
                   value="{{ old('anio', $premio->anio) }}" placeholder="Ejemplo: 2023" required>
        </div>

        <!-- Select para elegir la película (invisible por defecto) -->
        <div class="form-group" id="pelicula-select"
             style="display: {{ $premio->entidad_type == 'App\Models\Director' && $premio->pelicula_id ? 'block' : 'none' }};">
            <label for="pelicula_id">Película:</label>
            <select class="form-control" id="pelicula_id" name="pelicula_id">
                <option value="">Seleccione una película</option>
                @foreach($peliculas as $pelicula)
                    <option value="{{ $pelicula->id }}"
                        {{ $premio->pelicula_id == $pelicula->id ? 'selected' : '' }}>
                        {{ $pelicula->titulo }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label id="entidad-label" for="entidad_id">
                @if('entidad_type' === 'App\Models\Director')
                    Director:
                @elseif('entidad_type' === 'App\Models\Actor')
                    Actor:
                @else
                    Pelicula:
                @endif
            </label>
            <select class="form-control" id="entidad_id" name="entidad_id" required>
                @if($premio->entidad_type == 'App\Models\Pelicula')
                    @foreach($peliculas as $pelicula)
                        <option value="{{ $pelicula->id }}"
                            {{ $premio->entidad_id == $pelicula->id ? 'selected' : '' }}>
                            {{ $pelicula->titulo }}
                        </option>
                    @endforeach
                @elseif($premio->entidad_type == 'App\Models\Director')
                    @foreach($directores as $director)
                        <option value="{{ $director->id }}"
                            {{ $premio->entidad_id == $director->id ? 'selected' : '' }}>
                            {{ $director->nombre }}
                        </option>
                    @endforeach
                @elseif($premio->entidad_type == 'App\Models\Actor')
                    @foreach($actores as $actor)
                        <option value="{{ $actor->id }}"
                            {{ $premio->entidad_id == $actor->id ? 'selected' : '' }}>
                            {{ $actor->nombre }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>

        <button class="btn btn-primary" type="submit">Actualizar Premio</button>
        <a class="btn btn-secondary mx-2" href="{{ route('premios.show', $premio->id) }}">Volver</a>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const peliculaRadio = document.getElementById("pelicula");
            const directorRadio = document.getElementById("director");
            const actorRadio = document.getElementById("actor");
            const peliculaSelect = document.getElementById("pelicula-select");
            const entidadSelect = document.getElementById("entidad_id");
            const entidadLabel = document.getElementById("entidad-label");
            const premioEntidadId = "{{ $premio->entidad_id }}";

            const peliculas = @json($peliculas);
            const directores = @json($directores);
            const actores = @json($actores);

            function inicializarFormulario() {
                if (directorRadio.checked) {
                    peliculaSelect.style.display = "block";
                } else if (actorRadio.checked) {
                    peliculaSelect.style.display = "block";
                } else {
                    peliculaSelect.style.display = "none";
                }
            }

            function actualizarOpciones() {
                entidadSelect.innerHTML = "";

                if (peliculaRadio.checked) {
                    entidadLabel.textContent = "Película:";
                    cargarOpciones(peliculas, "titulo");
                } else if (directorRadio.checked) {
                    entidadLabel.textContent = "Director:";
                    cargarOpciones(directores, "nombre");
                } else if (actorRadio.checked) {
                    entidadLabel.textContent = "Actor:";
                    cargarOpciones(actores, "nombre");
                }
            }

            function cargarOpciones(opciones, textoPropiedad) {
                opciones.forEach(opcion => {
                    const optionElement = document.createElement("option");
                    optionElement.value = opcion.id;
                    optionElement.textContent = opcion[textoPropiedad];

                    // Mantener la selección anterior si corresponde
                    if (String(opcion.id) === premioEntidadId) {
                        optionElement.selected = true;
                    }

                    entidadSelect.appendChild(optionElement);
                });
            }

            inicializarFormulario();
            actualizarOpciones();

            peliculaRadio.addEventListener("change", function () {
                peliculaSelect.style.display = "none";
                actualizarOpciones();
            });

            directorRadio.addEventListener("change", function () {
                peliculaSelect.style.display = "block";
                actualizarOpciones();
            });

            actorRadio.addEventListener("change", function () {
                peliculaSelect.style.display = "block";
                actualizarOpciones();
            });
        });
    </script>
@endsection
@include('footer')
