<header class="header">
    <a href="#" class="logo">
        <img src="{{asset('images/LOGO_TRV_PAGINA_WEB.jpg')}}"  alt="Logo de La página">
    </a>
    <input class="menu-btn" type="checkbox" id="menu-btn" />
    <label class="menu-icon" for="menu-btn"><span class="navicon"></span></label>
    <ul class="menu">
        <li><a href="{{ route('peliculas.index') }}">Peliculas</a></li>
        <li><a href="{{ route('generos.index') }}">Géneros</a></li>
        <li><a href="{{ route('directores.index') }}">Directores</a></li>
        <li><a href="{{ route('premios.index') }}">Premios</a></li>
    </ul>
</header>
