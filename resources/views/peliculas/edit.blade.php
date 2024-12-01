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
                        {{ $pelicula->generos->contains($genero->id) ? 'checked' : '' }}> <!-- Marcar si el género está asociado -->
                    <label class="form-check-label" for="genero{{ $genero->id }}">{{ $genero->nombre }}</label>
                </div>
            @endforeach
        </div>

        <div class="form-group">
            <h3>Premios</h3>
            <input type="hidden" id="premios-eliminar" name="premios_eliminar" value="">
            <button class="btn btn-primary mb-3" type="button" onclick="agregarPremio()">Agregar Premio</button>
            <div id="premios-container">
                @foreach($pelicula->premios as $index => $premio)
                    <div class="premio-item" data-id="{{ $premio->id }}">
                        <input type="hidden" name="premios[{{ $index }}][id]" value="{{ $premio->id }}">

                        <div class="form-group">
                            <label for="premio-nombre-{{ $index }}">Nombre del Premio:</label>
                            <input type="text" name="premios[{{ $index }}][nombre]" id="premio-nombre-{{ $index }}" class="form-control" value="{{ $premio->nombre }}" required>
                        </div>
                        <div class="form-group">
                            <label for="premio-categoria-{{ $index }}">Categoría:</label>
                            <input type="text" name="premios[{{ $index }}][categoria]" id="premio-categoria-{{ $index }}" class="form-control" value="{{ $premio->categoria }}" required>
                        </div>
                        <div class="form-group">
                            <label for="premio-anio-{{ $index }}">Año:</label>
                            <input type="number" name="premios[{{ $index }}][anio]" id="premio-anio-{{ $index }}" class="form-control" value="{{ $premio->anio }}" required>
                        </div>

                        <button type="button" class="btn btn-danger mt-2 mb-4" onclick="eliminarPremio(this, {{ $premio->id }})">Eliminar</button>

                    </div>
                @endforeach
            </div>
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
        <a class="btn btn-secondary mx-2" href="{{ route('peliculas.show', $pelicula->id) }}">Volver</a>
    </form>

    <script>
        let premioCount = {{ count($pelicula->premios) }};
        function agregarPremio() {
            const container = document.getElementById('premios-container');
            const template = `
                <div class="premio-item">
                    <div class="form-group">
                        <label for="premio-nombre-${premioCount}">Nombre del Premio:</label>
                        <input type="text" name="premios[${premioCount}][nombre]" id="premio-nombre-${premioCount}" class="form-control" placeholder="Ejemplo: Oscar" required>
                    </div>
                    <div class="form-group">
                        <label for="premio-categoria-${premioCount}">Categoría:</label>
                        <input type="text" name="premios[${premioCount}][categoria]" id="premio-categoria-${premioCount}" class="form-control" placeholder="Ejemplo: Mejor Director" required>
                    </div>
                    <div class="form-group">
                        <label for="premio-anio-${premioCount}">Año:</label>
                        <input type="number" name="premios[${premioCount}][anio]" id="premio-anio-${premioCount}" class="form-control" placeholder="Ejemplo: 2022" required>
                    </div>

                    <button type="button" class="btn btn-danger mb-4" onclick="eliminarPremio(this)">Eliminar</button>
                </div>`;
            container.insertAdjacentHTML('beforeend', template);
            premioCount++;
        }

        function eliminarPremio(button, premioId = null) {
            if (confirm('¿Estás seguro de que deseas eliminar este premio?')) {

                const premiosEliminar = document.getElementById('premios-eliminar');

                if (premioId) {
                    const premiosAEliminar = premiosEliminar.value ? premiosEliminar.value.split(',') : [];
                    premiosAEliminar.push(premioId);
                    premiosEliminar.value = premiosAEliminar.join(',');
                }

                button.closest('.premio-item').remove();

            }
        }

        const peliculaShowUrl = "{{ route('peliculas.show', $pelicula->id) }}";

        document.getElementById('form-actualizar-pelicula').addEventListener('submit', function (event) {
            const premiosEliminar = document.getElementById('premios-eliminar').value;

            if (premiosEliminar) {
                const confirmacion = confirm('Tienes premios marcados para eliminar. ¿Estás seguro de que deseas continuar?');

                if (!confirmacion) {
                    // Si el usuario cancela, detenemos el envío del formulario
                    event.preventDefault();

                    window.location.href = peliculaShowUrl
                }
            }
        });

        let repartoArray = []; // Array global para almacenar los valores

        // Función para agregar un valor al array
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



            // Recorrer todas las opciones en el select "Reparto Seleccionado"
            Array.from(repartoSeleccionado.options).forEach(option => {
                agregarAlReparto(option.value);
            });

            console.log('Array inicializado:', repartoArray);

            // Mover actores de "disponibles" a "reparto seleccionado"
            agregarActorBtn.addEventListener('click', function () {
                Array.from(actoresDisponibles.selectedOptions).forEach(option => {
                    repartoArray.push(option.value); // Agregar al array
                    repartoSeleccionado.appendChild(option);
                });
            });

            // Mover actores de "reparto seleccionado" a "disponibles"
            removerActorBtn.addEventListener('click', function () {
                Array.from(repartoSeleccionado.selectedOptions).forEach(option => {
                    repartoArray = repartoArray.filter(id => id !== option.value); // Eliminar del array
                    actoresDisponibles.appendChild(option);
                });
            });
        });

        document.getElementById('form-actualizar-pelicula').addEventListener('submit', function () {
            const repartoSeleccionado = document.getElementById('reparto-seleccionado');
            repartoSeleccionado.innerHTML = ''; // Limpiar

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
