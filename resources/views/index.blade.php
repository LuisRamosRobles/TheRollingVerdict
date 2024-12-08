@extends('main')
@include('header')

@section('title', 'Inicio')

@section('content')
    <div class="container text-center" style="margin-top: 20px;">
        <h1>Bienvenido a The Rolling Verdict</h1>

        @if(auth()->check())

            <p class="mt-4">Hola, <strong>{{ $usuario->name }}</strong>. Aquí tienes las películas con mayor puntuación:</p>
            <div class="peliculas d-flex flex-wrap justify-content-center mt-4">
                @foreach($peliculas as $pelicula)
                    <a href="{{ route('peliculas.show', $pelicula->id) }}" class="text-decoration-none">
                        <div class="pelicula-card">
                            <img
                                src="{{ asset('storage/' . $pelicula->imagen) }}"
                                alt="{{ $pelicula->titulo }}"
                            >
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

            <p class="mt-4">Regístrate para que la gente sepa tu opinión sobre una película.</p>
            <div class="mt-4">
                <a href="{{ route('register') }}" class="btn btn-success mx-2">Regístrate</a>
                <a href="{{ route('login') }}" class="btn btn-primary mx-2">Iniciar sesión</a>
            </div>

            <h3 class="mt-5">Películas destacadas:</h3>
            <div class="peliculas d-flex flex-wrap justify-content-center mt-4">
                @foreach($peliculas  as $pelicula)
                    <a href="{{ route('peliculas.show', $pelicula->id) }}" class="text-decoration-none">
                        <div class="pelicula-card">
                            <img
                                src="{{ asset('storage/' . $pelicula->imagen) }}"
                                alt="{{ $pelicula->titulo }}"
                            >
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
        @endif
    </div>
@endsection

@include('footer')
