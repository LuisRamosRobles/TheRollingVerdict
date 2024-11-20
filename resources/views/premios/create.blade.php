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
            <input type="radio" id="pelicula" name="entidad_type" value="App\Models\Pelicula" checked>
            <label for="pelicula">Película</label>

            <input type="radio" id="director" name="entidad_type" value="App\Models\Director">
            <label for="director">Director</label>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre del Premio:</label>
            <input class="form-control" id="nombre" name="nombre" type="text" placeholder="Ejemplo: Oscar" required>
        </div>

        <div class="form-group">
            <label for="categoria">Categoría:</label>
            <input class="form-control" id="categoria" name="categoria" type="text" placeholder="Ejemplo: Mejor Película" required>
        </div>

        <div class="form-group">
            <label for="anio">Año:</label>
            <input class="form-control" id="anio" name="anio" type="number" placeholder="Ejemplo: 2023" required>
        </div>

        <div class="form-group">
            <label id="entidad-label" for="entidad_id"></label>
            <select class="form-control" id="entidad_id" name="entidad_id" required>

            </select>
        </div>

        <!-- Select para elegir la película (invisible por defecto) -->
        <div class="form-group" id="pelicula-select" style="display: none;">
            <label for="pelicula_id">Película:</label>
            <select class="form-control" id="pelicula_id" name="pelicula_id">
                <option value="">Seleccione una película</option>
                @foreach($peliculas as $pelicula)
                    <option value="{{ $pelicula->id }}">{{ $pelicula->titulo }}</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary" type="submit">Crear Premio</button>
        <a class="btn btn-secondary mx-2" href="{{ route('premios.index') }}">Volver</a>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const peliculaRadio = document.getElementById("pelicula");
            const directorRadio = document.getElementById("director");
            const peliculaSelect = document.getElementById("pelicula-select");
            const entidadSelect = document.getElementById("entidad_id");
            const entidadLabel = document.getElementById("entidad-label");


            // Cambiar visibilidad del select de películas según el radio seleccionado
            peliculaRadio.addEventListener("change", function () {
                if (peliculaRadio.checked) {
                    peliculaSelect.style.display = "none";
                }
            });

            directorRadio.addEventListener("change", function () {
                if (directorRadio.checked) {
                    peliculaSelect.style.display = "block";
                }
            });

            const peliculas = @json($peliculas);
            const directores = @json($directores);

            function actualizarOpciones() {
                // Limpiar el select
                entidadSelect.innerHTML = "";

                entidadLabel.textContent = peliculaRadio.checked ? "Película:" : "Director:";

                // Obtener los datos correctos según el tipo seleccionado
                const opciones = peliculaRadio.checked ? peliculas : directores;

                // Crear las opciones dinámicamente
                opciones.forEach(opcion => {
                    const optionElement = document.createElement("option");
                    optionElement.value = opcion.id;
                    optionElement.textContent = opcion.titulo || opcion.nombre;
                    entidadSelect.appendChild(optionElement);
                });
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

