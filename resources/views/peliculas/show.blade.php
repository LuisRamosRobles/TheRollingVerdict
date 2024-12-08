@php use App\Models\Pelicula;
     use App\Models\Director;
     use App\Models\Actor;
     use App\Models\User;
     use Carbon\Carbon; @endphp

@extends('main')
@include('header')

@section('title', $pelicula->titulo)

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
    @elseif ($errors->any())
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

    <div class="pelicula-detalle">
        <div class="info">
            <h1>{{ $pelicula->titulo }}</h1>
            <p><strong>Fecha de Estreno:</strong> {{ Carbon::parse($pelicula->estreno)->format('d-m-Y') }}</p>
            <p><strong>Sinopsis:</strong> {{ $pelicula->sinopsis }}</p>
            <h3>Géneros</h3>
            <ul>
                @foreach ($pelicula->generos as $genero)
                    <li>{{ $genero->nombre }}</li>
                @endforeach
            </ul>

            <h3>Director</h3>
            <div class="col-md-3">
                @if($pelicula->director)
                    <a href="{{ route('directores.show', ['id' => $pelicula->director->id, 'referer' => url()->current()]) }}" class="text-decoration-none">
                        <div class="director-card-pelicula">
                            <img
                                src="{{ $pelicula->director->imagen != Director::$IMAGEN_DEFAULT ? asset('storage/' . $pelicula->director->imagen) : Director::$IMAGEN_DEFAULT }}"
                                alt="Imagen de {{ $pelicula->director->nombre }}">
                            <h6>{{ $pelicula->director->nombre }}</h6>
                        </div>

                    </a>
                @else
                    <p>No hay un director asociado a esta película.</p>
                @endif
            </div>

            <h3>Reparto</h3>
            <div class="row">
                @if($pelicula->actores->isNotEmpty())
                    @foreach ($pelicula->actores as $actor)
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('actores.show', ['id' => $actor->id, 'referer' => url()->current()]) }}" class="text-decoration-none">
                                <div class="actor-card-pelicula">
                                    <img
                                        src="{{ $actor->imagen != Actor::$IMAGEN_DEFAULT ? asset('storage/' . $actor->imagen) : Actor::$IMAGEN_DEFAULT }}"
                                        alt="Imagen de {{ $actor->nombre }}">
                                    <h6>{{ $actor->nombre }}</h6>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @else
                    <p>No hay actores asociados con esta película.</p>
                @endif
            </div>

            <h3>Premios</h3>
            @if ($pelicula->premios->isNotEmpty())
                <ul>
                    @foreach ($pelicula->premios as $premio)
                        <li>
                            <strong>{{ $premio->nombre }}</strong> - {{ $premio->categoria }} ({{ $premio->anio }})
                        </li>
                    @endforeach
                </ul>
            @else
                <p>No hay premios asociados con esta película.</p>
            @endif

            <h3>Reseñas</h3>
            <div class="reseñas">
                @forelse($pelicula->resenas as $resena)
                    <div class="resena" style="border-bottom: 1px solid #ddd; padding: 15px 0; display: flex; gap: 10px;">

                        @if($resena->user->profile_image != User::$IMAGEN_DEFAULT)
                            <img
                                src="{{ asset('storage/' . $resena->user->profile_image) }}"
                                alt="Foto de perfil de {{ $resena->user->name }}"
                                class="profile-image"
                            >
                        @else
                            <img alt="Imagen por defecto" class="profile-image" src="{{ User::$IMAGEN_DEFAULT }}">
                        @endif


                        <div>

                            <p style="font-weight: bold; margin: 0;">{{ $resena->user->username }}</p>


                            <div class="rating" style="margin: 6px 0;">
                                @for($i = 5; $i >= 1; $i--)
                                    @if($i <= $resena->calificacion)
                                        <span style="color: gold; font-size: 20px;">&#9733;</span>
                                    @else
                                        <span style="color: lightgray; font-size: 20px;">&#9734;</span>
                                    @endif
                                @endfor
                            </div>


                            <p style="margin: 0;">{{ $resena->comentario }}</p>
                        </div>
                    </div>
                @empty
                    <p>No hay reseñas todavía. ¡Sé el primero en añadir una!</p>
                @endforelse
            </div>


        @if(auth()->check())
                <form method="POST" action="{{ route('resenas.store', $pelicula->id) }}" style="margin-top: 20px;">
                    @csrf
                    <h4>Añadir una reseña</h4>


                    <div class="mb-3" style="display: flex; align-items: center;">
                        <label for="calificacion" style="margin-right: 10px;">Calificación:</label>
                        <div class="rating">
                            <input type="radio" id="star5" name="calificacion" value="5" required />
                            <label for="star5" title="5 estrellas">★</label>

                            <input type="radio" id="star4" name="calificacion" value="4" required />
                            <label for="star4" title="4 estrellas">★</label>

                            <input type="radio" id="star3" name="calificacion" value="3" required />
                            <label for="star3" title="3 estrellas">★</label>

                            <input type="radio" id="star2" name="calificacion" value="2" required />
                            <label for="star2" title="2 estrellas">★</label>

                            <input type="radio" id="star1" name="calificacion" value="1" required />
                            <label for="star1" title="1 estrella">★</label>
                        </div>
                    </div>



                    <div class="mb-3">
                        <label for="comentario">Comentario:</label>
                        <textarea name="comentario" id="comentario" class="form-control" rows="4" placeholder="Escribe tu opinión..."></textarea>
                    </div>


                    <button type="submit" class="btn btn-primary">Enviar reseña</button>
                </form>
            @else
                <p>Inicia sesión para añadir una reseña. <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}">Iniciar sesión</a></p>
            @endif


        </div>


        <div class="imagen">
            @if($pelicula->imagen != Pelicula::$IMAGEN_DEFAULT)
                <img alt="Imagen de {{ $pelicula->titulo }}" class="img-fluid"
                     src="{{ asset('storage/' . $pelicula->imagen) }}"
                     width="230px" height="340px">
            @else
                <img alt="Imagen por defecto" class="img-fluid" src="{{ Pelicula::$IMAGEN_DEFAULT }}">
            @endif

            <div class="calificacion-media" style="text-align: center; margin-top: 10px;">

                <p style="font-size: 24px; font-weight: bold; display: flex; align-items: center; justify-content: center;">

                    <span>{{ number_format($pelicula->promedio_calificacion, 1) }} / 5</span>


                    <span style="margin-left: 10px; display: inline-block; transform: translateY(-3px);">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($pelicula->promedio_calificacion))
                                <span style="color: gold; font-size: 20px;">&#9733;</span>
                            @else
                                <span style="color: lightgray; font-size: 20px;">&#9734;</span>
                            @endif
                        @endfor
                    </span>
                </p>
            </div>
        </div>
    </div>

    @if(auth()->check() && auth()->user()->role === 'ADMIN')
        <a class="btn btn-secondary mb-4 mx-2" href="{{ url()->previous() }}">Volver</a>
    @else
        <a class="btn btn-secondary mb-4 mx-2" href="{{ $referer }}">Volver</a>
    @endif



@endsection
@include('footer')
