@php use App\Models\Actor;
     use Carbon\Carbon;
     use App\Models\Pelicula;
@endphp

@extends('main')
@include('header')

@section('title', $actor->nombre)

@section('content')

    @if(session('success'))
        <br>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <br>
    @endif

    <div class="actor-detalle">
        <div class="info">
            <h1>{{ $actor->nombre }}</h1>
            <p><strong>Edad:</strong>
                {{ $actor->fecha_nac ? Carbon::parse($actor->fecha_nac)->format('d-m-Y') . ' (' . $actor->anios_edad . ' años)' : 'No disponible'}}
            </p>
            <p><strong>Lugar de nacimiento:</strong> {{ $actor->lugar_nac }}</p>
            <p><strong>Biografía:</strong> {{$actor->biografia}}</p>
            <p><strong>Años activos:</strong>
                {{ $actor->inicio_actividad
        ? ($actor->inicio_actividad . ' - ' . ($actor->fin_actividad ?? 'Presente') . ' (' . $actor->anios_activo . ' años)')
        : 'No disponible' }}
            </p>
            <p><strong>Activo:</strong>{{ $actor->activo ? 'Sí' : 'No' }}</p>

            <h3>Premios:</h3>
            @if ($actor->premios->isNotEmpty())
                <ul>
                    @foreach ($actor->premios as $premio)
                        <li>
                            <strong>{{ $premio->nombre }}</strong> - {{ $premio->categoria }} ({{ $premio->anio }})
                            @if ($premio->pelicula)
                                - <a href="{{ route('peliculas.show', $premio->pelicula->id) }}">{{ $premio->pelicula->titulo }}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p>No hay premios registrados para <strong>{{ $actor->nombre }}</strong>.</p>
            @endif
        </div>
        <div class="imagen">
            @if($actor->imagen != Actor::$IMAGEN_DEFAULT)
                <img alt="Imagen de {{ $actor->nombre }}" class="img-fluid"
                     src="{{ asset('storage/' . $actor->imagen) }}"
                     width="230px" height="340px">
            @else
                <img alt="Imagen por defecto" class="img-fluid"
                     src="{{ Actor::$IMAGEN_DEFAULT }}">
            @endif
        </div>
    </div>

    <div class="peliculas-actuadas mt-5">
        <h2>Películas Actuadas</h2>
        <div class="row">
            @if(count($peliculas) > 0)
                @foreach($peliculas as $pelicula)
                    <div class="col-md-3">
                        <a href="{{ route('peliculas.show', $pelicula->id) }}" class="text-decoration-none">
                            <div class="card h-100">
                                <div class="card-body">
                                    @if($pelicula->imagen!= Pelicula::$IMAGEN_DEFAULT)
                                        <img alt="Imagen de {{ $pelicula->titulo }}" class="img-fluid"
                                             src="{{ asset('storage/'. $pelicula->imagen) }}"
                                             width="380px" height="220px">
                                    @else
                                        <img alt="Imagen por defecto" class="img-fluid"
                                             src="{{ Pelicula::$IMAGEN_DEFAULT }}">
                                    @endif

                                    <h6 class="card-title">{{ $pelicula->titulo }}</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            @else
                <p>No se han encontrado películas actuadas para <strong>{{ $actor->nombre }}</strong>.</p>
            @endif
        </div>
        <div class="pagination-container">
            {{$peliculas->links('pagination::bootstrap-4')}}
        </div>
    </div>

    <a class="btn btn-success mb-4" href="{{route('actores.edit', $actor->id)}}">Actualizar Actor</a>
    <a class="btn btn-secondary mb-4 mx-2" href="{{ route('actores.index') }}">Volver</a>
    <form action="{{ route('actores.destroy', $actor->id) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger mb-4"
                onclick="return confirm('¿Estás seguro de que deseas borrar este actor?')">Borrar
        </button>
    </form>

@endsection
@include('footer')
