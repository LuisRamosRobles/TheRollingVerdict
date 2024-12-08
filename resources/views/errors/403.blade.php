@extends('main')
@include('header')

@section('title', 'Error 403')

@section('content')
    <div class="text-center" style="margin-top: 100px;">
        <h1 style="font-size: 80px; color: #ff6f61;">403</h1>
        <h2>Acceso Denegado</h2>
        <p>No eres digno de acceder a esta pagina.</p>
        <img src="{{ asset('images/403.gif') }}">

    </div>
    <a href="{{ route('index') }}" class="btn btn-primary">Volver al Inicio</a>
@endsection

@include('footer')
