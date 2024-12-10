@extends('main')
@include('header')

@section('title', 'Reseñas de ' . $pelicula->titulo)

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


    <h1>Reseñas de "{{ $pelicula->titulo }}"</h1>
    <a class="btn btn-secondary mb-3" href="{{ route('admin.resenas') }}">Volver</a>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>Usuario</th>
                <th>Calificación</th>
                <th>Comentario</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody>
            @foreach($pelicula->resenas as $resena)
                <tr>
                    <td>{{ $resena->user->username }}</td>
                    <td>
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $resena->calificacion)
                                <span style="color: gold;">&#9733;</span>
                            @else
                                <span style="color: lightgray;">&#9734;</span>
                            @endif
                        @endfor
                    </td>
                    <td>{{ $resena->comentario }}</td>
                    <td>
                        <form action="{{ route('admin.resenas.destroy', $resena->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta reseña?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
@include('footer')

