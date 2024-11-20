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
            <label for="reparto">Reparto:</label>
            <input class="form-control" id="reparto" name="reparto" type="text" required>
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
        <a class="btn btn-secondary mx-2" href="{{ route('peliculas.index') }}">Volver</a>
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
