@extends('main')
@include('header')

@section('title', 'Añadir Actor')

@section('content')
    <h1>Añadir Actor</h1>

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

    <form action="{{ route('actores.store') }}" method="post" enctype="multipart/form-data">
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
            <label for="inicio_actividad">Año de inicio de actividad (Formato: AAAA):</label>
            <input class="form-control" id="inicio_actividad" name="inicio_actividad" type="number" placeholder="AAAA">
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

        <div class="form-group" id="input-fin_actividad"
             style="display: none">
            <label for="fin_actividad">Año de fin de actividad (Formato: AAAA):</label>
            <input class="form-control" id="fin_actividad" name="fin_actividad" type="number" placeholder="AAAA">
        </div>

        <div class="form-group">
            <label for="imagen">Imagen:</label>
            <input accept="image/*" class="form-control-file" id="imagen" name="imagen" type="file">
            <small class="form-text text-muted">Tipos de archivos compatibles: jpeg,png,jpg,gif,svg</small>
        </div>

        <button class="btn btn-primary" type="submit">Crear</button>
        <a class="btn btn-secondary mx-2" href="{{ route('admin.actores') }}">Volver</a>
    </form>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const activoSiRadio = document.getElementById('activo_si');
            const activoNoRadio = document.getElementById('activo_no');
            const finActividadInput = document.getElementById('input-fin_actividad');

            function actualizarVisibilidadFinActividad() {
                if (activoNoRadio.checked) {
                    finActividadInput.style.display = "block";
                } else {
                    finActividadInput.style.display = "none";
                    finActividadInput.value = '';
                }
            }


            actualizarVisibilidadFinActividad();


            activoSiRadio.addEventListener('change', actualizarVisibilidadFinActividad);
            activoNoRadio.addEventListener('change', actualizarVisibilidadFinActividad);

        });
    </script>

@endsection
@include('footer')
