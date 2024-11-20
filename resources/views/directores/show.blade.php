@php use App\Models\Director;
     use Carbon\Carbon;
     use App\Models\Pelicula@endphp

@extends('main')
@include('header')

@section('title', $director->nombre)

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

    <div class="director-detalle">
        <div class="info">
            <h1>{{ $director->nombre }}</h1>
            <p><strong>Edad:</strong>
                {{ $director->fecha_nac ? Carbon::parse($director->fecha_nac)->format('d-m-Y') . ' (' . $director->anios_edad . ' años)' : 'No disponible' }}
            </p>
            <p><strong>Lugar de nacimiento:</strong> {{ $director->lugar_nac }}</p>
            <p><strong>Biografía:</strong> {{$director->biografia}}</p>
            <p><strong>Inicio de actividad:</strong>
                {{ $director->inicio_actividad ? Carbon::parse($director->inicio_actividad)->format('Y') .' ('. $director->anios_activo . ' años)' : 'No disponible'}}
            </p>
            <p><strong>Activo:</strong> {{ $director->activo ? 'Sí' : 'No' }}</p>

            <h3>Premios:</h3>
            @if ($director->premios->isNotEmpty())
                <ul>
                    @foreach ($director->premios as $premio)
                        <li>
                            <strong>{{ $premio->nombre }}</strong> - {{ $premio->categoria }} ({{ $premio->anio }})
                            @if ($premio->pelicula)
                                - <a href="{{ route('peliculas.show', $premio->pelicula->id) }}">{{ $premio->pelicula->titulo }}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p>No hay premios registrados.</p>
            @endif
        </div>
        <div class="imagen">
            @if($director->imagen != Director::$IMAGEN_DEFAULT)
                <img alt="Imagen de {{ $director->nombre }}" class="img-fluid"
                     src="{{ asset('storage/' . $director->imagen) }}"
                     width="230px" height="340px">
            @else
                <img alt="Imagen por defecto" class="img-fluid"
                     src="{{ Director::$IMAGEN_DEFAULT }}">
            @endif
        </div>
    </div>


    <div class="peliculas-dirigidas mt-5">
        <h2>Películas Dirigidas</h2>
        <div class="row">
            @if(count($peliculas) > 0)
                @foreach($peliculas as $pelicula)
                    <div class="col-md-3">
                        <a href="{{ route('peliculas.show', $pelicula->id) }}" class="text-decoration-none">
                            <div class="card h-100">
                                <div class="card-body">
                                    @if($pelicula->imagen != Pelicula::$IMAGEN_DEFAULT)
                                        <img alt="Imagen de {{ $pelicula->titulo }}" class="img-fluid"
                                             src="{{ asset('storage/' . $pelicula->imagen) }}"
                                             width="380px" height="220px">
                                    @else
                                        <img alt="Imagen por defecto" class="img-fluid"
                                             src="{{Pelicula::$IMAGEN_DEFAULT}}">
                                    @endif
                                    <h6 class="card-title">{{ $pelicula->titulo }}</h6>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            @else
                <p>No se han encontrado películas para este director.</p>
            @endif
        </div>
        <div class="pagination-container">
            {{$peliculas->links('pagination::bootstrap-4')}}
        </div>
    </div>


    <a class="btn btn-success mb-4" href="{{route('directores.edit', $director->id)}}">Actualizar Director</a>
    <a class="btn btn-secondary mb-4 mx-2" href="{{ route('directores.index') }}">Volver</a>
    <form action="{{ route('directores.destroy', $director->id) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger mb-4"
                onclick="return confirm('¿Estás seguro de que deseas borrar este director?')">Borrar
        </button>
    </form>

@endsection
@include('footer')
