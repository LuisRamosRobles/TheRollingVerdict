@extends('main')
@include('header')

@section('title', 'Error 404')

@section('content')
    <div class="text-center" style="margin-top: 100px;">
        <h1 style="font-size: 80px; color: #ffcc00;">404</h1>
        <h2>Página No Encontrada</h2>
        <p>Parece que no hay nada por aquí.</p>
        <img src="{{ asset('images/404.gif') }}">
    </div>
    <a href="{{ route('index') }}" class="btn btn-primary">Volver al Inicio</a>
@endsection

@include('footer')
