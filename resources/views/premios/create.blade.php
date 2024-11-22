@extends('main')
@include('header')

@section('title', 'Añadir Premio')

@section('content')


    <h1>Añadir Premio</h1>

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

    <form action="{{ route('premios.store') }}" method="post">
        @csrf
        @method('POST')

        <div class="form-group">
            <label for="entidad-titulo">¿Para quién es el Premio?</label><br>
            <input type="radio" id="pelicula" name="entidad_type" value="App\Models\Pelicula"
                {{ old('entidad_type', 'App\Models\Pelicula') === 'App\Models\Pelicula' ? 'checked' : '' }}>
            <label for="pelicula">Película</label>

            <input type="radio" id="director" name="entidad_type" value="App\Models\Director"
                {{ old('entidad_type') === 'App\Models\Director' ? 'checked' : '' }}>
            <label for="director">Director</label>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre del Premio:</label>
            <input class="form-control" id="nombre" name="nombre" type="text"
                   value="{{ old('nombre') }}" placeholder="Ejemplo: Oscar" required>
        </div>

        <div class="form-group">
            <label for="categoria">Categoría:</label>
            <input class="form-control" id="categoria" name="categoria" type="text"
                   value="{{ old('categoria') }}" placeholder="Ejemplo: Mejor Película" required>
        </div>

        <div class="form-group">
            <label for="anio">Año:</label>
            <input class="form-control" id="anio" name="anio" type="number"
                   value="{{ old('anio') }}" placeholder="Ejemplo: 2023" required>
        </div>

        <div class="form-group">
            <label id="entidad-label" for="entidad_id">
                {{ old('entidad_type', 'App\Models\Pelicula') === 'App\Models\Pelicula' ? 'Película:' : 'Director:' }}
            </label>
            <select class="form-control" id="entidad_id" name="entidad_id" required>

            </select>
        </div>


        <div class="form-group" id="pelicula-select"
             style="display: {{ old('entidad_type') === 'App\Models\Director' ? 'block' : 'none' }};">
            <label for="pelicula_id">Película:</label>
            <select class="form-control" id="pelicula_id" name="pelicula_id">
                <option value="">Seleccione una película</option>
                @foreach($peliculas as $pelicula)
                    <option value="{{ $pelicula->id }}" {{ old('pelicula_id') == $pelicula->id ? 'selected' : '' }}>
                        {{ $pelicula->titulo }}
                    </option>
                @endforeach
            </select>
        </div>


        <div class="mt-5" id="grupo-botones">
            <button class="btn btn-primary" type="submit">Crear Premio</button>
            <a class="btn btn-secondary mx-2" href="{{ route('premios.index') }}">Volver</a>
            <button class="btn btn-danger mx-2" type="button" onclick="limpiarformulario()">Limpiar Formulario</button>
        </div>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const peliculaRadio = document.getElementById("pelicula");
            const directorRadio = document.getElementById("director");
            const peliculaSelect = document.getElementById("pelicula-select");
            const entidadSelect = document.getElementById("entidad_id");
            const entidadLabel = document.getElementById("entidad-label");

            const peliculas = @json($peliculas);
            const directores = @json($directores);

            const oldEntidadType = "{{ old('entidad_type', 'App\Models\Pelicula') }}";
            const oldEntidadId = "{{ old('entidad_id') }}";

            function actualizarOpciones() {
                // Limpiar el select
                entidadSelect.innerHTML = "";

                // Cambiar el label según el tipo seleccionado
                entidadLabel.textContent = peliculaRadio.checked ? "Película:" : "Director:";

                // Obtener las opciones correctas según el tipo seleccionado
                const opciones = peliculaRadio.checked ? peliculas : directores;

                // Crear las opciones dinámicamente
                opciones.forEach(opcion => {
                    const optionElement = document.createElement("option");
                    optionElement.value = opcion.id;
                    optionElement.textContent = peliculaRadio.checked ? opcion.titulo : opcion.nombre;

                    // Seleccionar la opción si coincide con el valor anterior
                    if (String(opcion.id) === oldEntidadId) {
                        optionElement.selected = true;
                    }

                    entidadSelect.appendChild(optionElement);
                });
            }

            // Inicializa el estado de los radios y el select
            if (oldEntidadType === "App\Models\Director") {
                directorRadio.checked = true;
                peliculaSelect.style.display = "block";
            } else {
                peliculaRadio.checked = true;
                peliculaSelect.style.display = "none";
            }

            // Eventos para actualizar el select cuando cambie el radio button
            peliculaRadio.addEventListener("change", function () {
                peliculaSelect.style.display = "none";
                actualizarOpciones();
            });
            directorRadio.addEventListener("change", function () {
                peliculaSelect.style.display = "block";
                actualizarOpciones();
            });

            // Inicializar las opciones al cargar la página
            actualizarOpciones();
        });
    </script>
@endsection
@include('footer')

