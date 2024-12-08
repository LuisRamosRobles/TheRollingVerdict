@php use App\Models\Actor;
     use Carbon\Carbon @endphp

@extends('main')
@include('header')

@section('title', 'Actualizar Actor')

@section('content')

    <h1>Actualizar Actor</h1>

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

    <form action="{{ route('actores.update', $actor->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre"
                   value="{{ $actor->nombre }}">
        </div>

        <div class="form-group">
            <label for="fecha_nac">Fecha de Nacimiento (Formato: AAAA-MM-DD):</label>
            <input type="text" class="form-control" id="fecha_nac" name="fecha_nac"
                   value="{{ $actor->fecha_nac ? Carbon::parse($actor->fecha_nac)->format('Y-m-d') : ''}}">
        </div>

        <div class="form-group">
            <label for="lugar_nac">Lugar de Nacimiento:</label>
            <input type="text" class="form-control" id="lugar_nac" name="lugar_nac"
                   value="{{ $actor->lugar_nac }}">
        </div>

        <div class="form-group">
            <label for="biografia">Biografía:</label>
            <textarea class="form-control" id="biografia" name="biografia" rows="3">{{ old('biografia', $actor->biografia) }}</textarea>
        </div>

        <div class="form-group">
            <label for="inicio_actividad">Año de inicio de actividad (Formato: AAAA):</label>
            <input class="form-control" id="inicio_actividad" name="inicio_actividad" type="number" placeholder="AAAA"
                    value="{{ $actor->inicio_actividad }}">
        </div>

        <div class="form-group">
            <label>¿Está activo?</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="activo" id="activo_si" value="1"
                    {{ $actor->activo ? 'checked' : '' }}>
                <label class="form-check-label" for="activo_si">Sí</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="activo" id="activo_no" value="0"
                    {{ !$actor->activo ? 'checked' : '' }}>
                <label class="form-check-label" for="activo_no">No</label>
            </div>
        </div>

        <div class="form-group" id="input-fin_actividad"
             style="display: none">
            <label for="fin_actividad">Año de fin de actividad (Formato: AAAA):</label>
            <input class="form-control" id="fin_actividad" name="fin_actividad" type="number" placeholder="AAAA"
                value="{{ $actor->fin_actividad }}">
        </div>

        <div class="form-group imagen">
            <label for="imagen">Imagen:</label>
            @if ($actor->imagen != Actor::$IMAGEN_DEFAULT)
                <img alt="Imagen de {{ $actor->nombre }}" class="img-fluid" src="{{ asset('storage/'. $actor->imagen)}}"
                     width="230px" height="340px">
            @else
                <img alt="Imagen por defecto" class="img-fluid" src="{{ Actor::$IMAGEN_DEFAULT }}">
            @endif
            <br>
            <br>
            <input accept="image/*" class="form-control-file" id="imagen" name="imagen" type="file">
            <small class="form-text text-muted">Tipos de archivos compatibles: jpeg,png,jpg,gif,svg</small>
        </div>

        <button class="btn btn-primary" type="submit">Actualizar</button>
        <a class="btn btn-secondary mx-2" href="{{ route('admin.actores') }}">Volver</a>

    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const activoSiRadio = document.getElementById('activo_si');
            const activoNoRadio = document.getElementById('activo_no');
            const finActividadDiv = document.getElementById('input-fin_actividad');
            const finActividadInput = document.getElementById('fin_actividad');



            function actualizarVisibilidadFinActividad() {
                if (activoNoRadio.checked) {
                    finActividadDiv.style.display = 'block';
                } else {
                    finActividadDiv.style.display = 'none';
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

