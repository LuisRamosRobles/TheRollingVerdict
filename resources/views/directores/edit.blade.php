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
            <a class="btn btn-secondary mx-2" href="{{ route('admin.directores') }}">Volver</a>
        </form>
    </div>

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
@include('header')
