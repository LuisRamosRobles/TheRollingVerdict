@php use App\Models\Director;
     use Carbon\Carbon;@endphp

@extends('main')
@include('header')

@section('title', 'Actualizar Director')

@section('content')

    <div class="container mt-6">
        <h1>Actualizar Director</h1>

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

        <form id="form-actualizar-director" action="{{ route('directores.update', $director->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input class="form-control" id="nombre" name="nombre" type="text" required
                       value="{{$director->nombre}}">
            </div>

            <div class="form-group">
                <label for="fecha_nac">Fecha de Nacimiento (Formato: AAAA-MM-DD):</label>
                <input class="form-control" id="fecha_nac" name="fecha_nac" type="text" placeholder="AAAA-MM-DD"
                       value="{{$director->fecha_nac ? Carbon::parse($director->fecha_nac)->format('Y-m-d') : ''}}">
            </div>

            <div class="form-group">
                <label for="lugar_nac">Lugar de Nacimiento:</label>
                <input class="form-control" id="lugar_nac" name="lugar_nac" type="text"
                       value="{{$director->lugar_nac}}">
            </div>

            <div class="form-group">
                <label for="biografia">Biografía:</label>
                <textarea class="form-control" id="biografia" name="biografia" rows="3">{{ old('biografia', $director->biografia) }}</textarea>
            </div>

            <div class="form-group">
                <label for="inicio_actividad">Año inicio de Actividad (Formato: AAAA):</label>
                <input class="form-control" id="inicio_actividad" name="inicio_actividad" type="number" placeholder="AAAA"
                       value="{{ $director->inicio_actividad }}">
            </div>

            <div class="form-group">
                <label>¿Está activo?</label><br>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="activo" id="activo_si" value="1"
                        {{ $director->activo ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo_si">Sí</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="activo" id="activo_no" value="0"
                        {{ !$director->activo ? 'checked' : '' }}>
                    <label class="form-check-label" for="activo_no">No</label>
                </div>

            </div>

            <div class="form-group" id="input-fin_actividad"
                 style="display: none">
                <label for="fin_actividad">Año de fin de actividad (Formato: AAAA):</label>
                <input class="form-control" id="fin_actividad" name="fin_actividad" type="number" placeholder="AAAA"
                       value="{{ $director->fin_actividad }}">
            </div>

            <div class="form-group">
                <h3>Premios</h3>
                <input type="hidden" id="premios-eliminar" name="premios_eliminar" value="">
                <button class="btn btn-primary mb-3" type="button" onclick="agregarPremio()">Agregar Premio</button>
                <div id="premios-container">
                    @foreach($director->premios as $index => $premio)
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
                            <div class="form-group">
                                <label for="premio-pelicula-{{ $index }}">Película:</label>
                                <select name="premios[{{ $index }}][pelicula_id]" id="premio-pelicula-{{ $index }}" class="form-control">
                                    <option value="">Selecciona una película</option>
                                    @foreach($peliculas as $pelicula)
                                        <option value="{{ $pelicula->id }}" {{ $premio->pelicula_id == $pelicula->id ? 'selected' : '' }}>
                                            {{ $pelicula->titulo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-danger mt-2 mb-4" onclick="eliminarPremio(this, {{ $premio->id }})">Eliminar</button>

                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-group imagen">
                <label for="imagen">Imagen:</label>
                @if($director->imagen != Director::$IMAGEN_DEFAULT)
                    <img alt="Imagen de {{ $director->nombre }}" class="img-fluid" src="{{asset('storage/' . $director->imagen)}}"
                         width="230px" height="340px" >
                @else
                    <img alt="Imagen por defecto" class="img-fluid" src="{{ Director::$IMAGEN_DEFAULT }}">
                @endif
                <br>
                <br>
                <input accept="image/*" class="form-control-file" id="imagen" name="imagen" type="file">
                <small class="form-text text-muted">Tipos de archivos compatibles: jpeg,png,jpg,gif,svg</small>
            </div>

            <button class="btn btn-primary" type="submit">Actualizar</button>
            <a class="btn btn-secondary mx-2" href="{{ route('directores.show', $director->id) }}">Volver</a>
        </form>
    </div>

    <script>
        let premioCount = {{ count($director->premios) }};
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
                    <div class="form-group">
                        <label for="premio-pelicula-${premioCount}">Película:</label>
                        <select name="premios[${premioCount}][pelicula_id]" id="premio-pelicula-${premioCount}" class="form-control">
                                <option value="">Selecciona una película</option>
                            @foreach($peliculas as $pelicula)
                                <option value="{{ $pelicula->id }}">{{ $pelicula->titulo }}</option>
                            @endforeach
                        </select>
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

        const directorShowUrl = "{{ route('directores.show', $director->id) }}";

        document.getElementById('form-actualizar-director').addEventListener('submit', function (event) {
            const premiosEliminar = document.getElementById('premios-eliminar').value;

            if (premiosEliminar) {
                const confirmacion = confirm('Tienes premios marcados para eliminar. ¿Estás seguro de que deseas continuar?');

                if (!confirmacion) {
                    // Si el usuario cancela, detenemos el envío del formulario
                    event.preventDefault();

                    window.location.href = directorShowUrl
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const activoSiRadio = document.getElementById('activo_si');
            const activoNoRadio = document.getElementById('activo_no');
            const finActividadDiv = document.getElementById('input-fin_actividad');
            const finActividadInput = document.getElementById('fin_actividad');


            // Función para actualizar la visibilidad del campo
            function actualizarVisibilidadFinActividad() {
                if (activoNoRadio.checked) {
                    finActividadDiv.style.display = 'block';
                } else {
                    finActividadDiv.style.display = 'none';
                    finActividadInput.value = '';
                }
            }

            // Establecer el estado inicial
            actualizarVisibilidadFinActividad();

            // Actualizar la visibilidad al cambiar el estado de los radio buttons
            activoSiRadio.addEventListener('change', actualizarVisibilidadFinActividad);
            activoNoRadio.addEventListener('change', actualizarVisibilidadFinActividad);
        });

    </script>
@endsection
@include('header')
