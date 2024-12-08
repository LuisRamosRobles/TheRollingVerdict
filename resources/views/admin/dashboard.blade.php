@extends('main')
@include('header')

@section('title', 'Dashboard Admin')

@section('content')
    <h1>Bienvenido al Dashboard de Administrador</h1>
    <p>Selecciona una sección para empezar a gestionar:</p>
    <div class="row">
        <div class="col-md-4 mb-4">
            <a href="{{ route('admin.peliculas') }}" class="card dashboard-card text-decoration-none text-center">
                <div class="card-body">
                    <h5 class="card-title">Películas</h5>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-4">
            <a href="{{ route('admin.generos') }}" class="card dashboard-card text-decoration-none text-center">
                <div class="card-body">
                    <h5 class="card-title">Géneros</h5>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-4">
            <a href="{{ route('admin.directores') }}" class="card dashboard-card text-decoration-none text-center">
                <div class="card-body">
                    <h5 class="card-title">Directores</h5>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-4">
            <a href="{{ route('admin.actores') }}" class="card dashboard-card text-decoration-none text-center">
                <div class="card-body">
                    <h5 class="card-title">Actores</h5>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-4">
            <a href="{{ route('admin.premios') }}" class="card dashboard-card text-decoration-none text-center">
                <div class="card-body">
                    <h5 class="card-title">Premios</h5>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-4">
            <a href="{{ route('admin.resenas') }}" class="card dashboard-card text-decoration-none text-center">
                <div class="card-body">
                    <h5 class="card-title">Reseñas</h5>
                </div>
            </a>
        </div>
    </div>
@endsection

@include('footer')
