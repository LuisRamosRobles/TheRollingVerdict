@extends('main')
@include('header')

@section('title', 'Añadir Director')

@section('content')
    <h1>Añadir Director</h1>

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

    <form action="{{ route('directores.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input class="form-control" id="nombre" name="nombre" type="text" required>
        </div>

        <div class="form-group">
            <label for="fecha_nac">Fecha de Nacimiento (Formato: AAAA-MM-DD):</label>
            <input class="form-control" id="fecha_nac" name="fecha_nac" type="text" placeholder="AAAA-MM-DD">
        </div>

        <div class="form-group">
            <label for="lugar_nac">Lugar de Nacimiento:</label>
            <input class="form-control" id="lugar_nac" name="lugar_nac" type="text">
        </div>

        <div class="form-group">
            <label for="biografia">Biografía:</label>
            <textarea class="form-control" id="biografia" name="biografia" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="inicio_actividad">Inicio de Actividad (Formato: AAAA-MM-DD):</label>
            <input class="form-control" id="inicio_actividad" name="inicio_actividad" type="text" placeholder="AAAA-MM-DD">
        </div>

        <div class="form-group">
            <label>¿Está activo?</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="activo" id="activo_si" value="1" checked>
                <label class="form-check-label" for="activo_si">Sí</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="activo" id="activo_no" value="0">
                <label class="form-check-label" for="activo_no">No</label>
            </div>
        </div>

        <div class="form-group">
            <h3>Premios</h3>
            <div id="premios-container">
                <button class="btn btn-primary mb-3" type="button" onclick="agregarPremio()">Agregar Premio</button>
            </div>
        </div>

        <div class="form-group">
            <label for="imagen">Imagen:</label>
            <input accept="image/*" class="form-control-file" id="imagen" name="imagen" type="file">
            <small class="form-text text-muted">Tipos de archivos compatibles: jpeg,png,jpg,gif,svg</small>
        </div>

        <button class="btn btn-primary" type="submit">Crear</button>
        <a class="btn btn-secondary mx-2" href="{{ route('directores.index') }}">Volver</a>
    </form>

    <script>
        let premioCount = 0;

        function agregarPremio() {
            const container = document.getElementById('premios-container');
            const newPremio = document.createElement('div');
            newPremio.classList.add('premio-row', 'mb-3');
            newPremio.innerHTML = `
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
        <button type="button" class="btn btn-danger" onclick="eliminarPremio(this)">Eliminar</button>
    `;
            container.appendChild(newPremio);
            premioCount++;
        }

        function eliminarPremio(button) {
            button.parentElement.remove();
        }
    </script>



@endsection
@include('footer')
