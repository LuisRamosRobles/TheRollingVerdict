@extends('main')
@include('header')

@section('title', 'Login')

@section('content')
    <x-guest-layout>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="p-8">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email/Nombre de Usuario')" />
                <x-text-input id="email" class="block mt-1 w-full" type="text" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-input-label for="password" :value="__('Contraseña')" />

                <x-text-input id="password" class="block mt-1 w-full"
                              type="password"
                              name="password"
                              required autocomplete="current-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('register') }}">
                    {{ __('¿No estás registrado?') }}
                </a>

                <x-primary-button class="ms-3">
                    {{ __('Iniciar Sesión') }}
                </x-primary-button>
            </div>
        </form>
    </x-guest-layout>

@endsection
@include('footer')
