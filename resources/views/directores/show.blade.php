@php use App\Models\Director;
     use Carbon\Carbon;
     use App\Models\Pelicula;
@endphp

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
            <p><strong>Años activos:</strong>
                {{ $director->inicio_actividad
         ? ($director->inicio_actividad . ' - ' . ($director->fin_actividad ?? 'Presente') . ' (' . $director->anios_activo . ' años)')
         : 'No disponible' }}
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
                <p>No hay premios registrados para <strong>{{ $director->nombre }}</strong>.</p>
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
                <div class="d-flex flex-wrap justify-content-center mt-4">
                    @foreach($peliculas as $pelicula)
                        <a href="{{ route('peliculas.show', ['id' => $pelicula->id, 'referer' => url()->current()]) }}" class="text-decoration-none">
                            <div class="pelicula-card">
                                <img
                                    src="{{ $pelicula->imagen != Pelicula::$IMAGEN_DEFAULT ? asset('storage/' . $pelicula->imagen) : Pelicula::$IMAGEN_DEFAULT }}"
                                    alt="Imagen de {{ $pelicula->titulo }}">


                                <div class="hover-content">
                                    <div class="nota-media">{{ number_format($pelicula->promedio_calificacion, 1) }}</div>
                                    <div class="rating">

                                        @for($i = 5; $i >= 1; $i--)
                                            @if($i <= floor($pelicula->promedio_calificacion))
                                                <span>&#9733;</span>
                                            @elseif($i == ceil($pelicula->promedio_calificacion))
                                                <span>&#9734;</span>
                                            @else
                                                <span class="empty">&#9733;</span>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="lead"><em>No se ha encontrado ninguna película registrada.</em></p>
            @endif
        </div>
        <div class="pagination-container">
            {{$peliculas->links('pagination::bootstrap-4')}}
        </div>
    </div>

    @if(auth()->check() && auth()->user()->role === 'ADMIN')
        <a class="btn btn-secondary mb-4 mx-2" href="{{ url()->previous() }}">Volver</a>
    @else
        <a class="btn btn-secondary mb-4 mx-2" href="{{ $referer }}">Volver</a>
    @endif

@endsection
@include('footer')
