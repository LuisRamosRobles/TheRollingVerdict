<header class="header">
    <a href="{{ route('index') }}" class="logo">
        <img src="{{ asset('images/LOGO_TRV_PAGINA_WEB.jpg') }}" alt="Logo de La página">
    </a>
    <input class="menu-btn" type="checkbox" id="menu-btn">
    <label class="menu-icon" for="menu-btn">
        <span class="navicon"></span>
    </label>
    <ul class="menu">
        <li><a href="{{ route('peliculas.index') }}">Peliculas</a></li>
        <li><a href="{{ route('generos.index') }}">Géneros</a></li>
        <li><a href="{{ route('directores.index') }}">Directores</a></li>
        <li><a href="{{ route('actores.index') }}">Actores</a></li>
        <li><a href="{{ route('premios.index') }}">Premios</a></li>
        @auth
            <li class="user-menu">
                <a href="#" class="user-name">{{ auth()->user()->name }}</a>
                <ul class="dropdown">
                    @if(auth()->user()->role === 'ADMIN')
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    @elseif(auth()->user()->role === 'USER')
                        <li><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                    @endif
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="logout-button">Cerrar Sesión</button>
                        </form>
                    </li>
                </ul>
            </li>
        @else
            <li><a href="{{ route('login') }}">Iniciar Sesión</a></li>
        @endauth
    </ul>
</header>
