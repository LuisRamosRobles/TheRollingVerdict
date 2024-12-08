<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\Director;
use App\Models\Genero;
use App\Models\Pelicula;
use App\Models\Premio;
use Illuminate\Http\Request;


class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function peliculas(Request $request)
    {
        $peliculas = Pelicula::search($request->search)->orderBy('estreno', 'desc')->get();

        return view('admin.peliculas.index', compact('peliculas'));
    }

    public function generos(Request $request)
    {
        $generos = Genero::search($request->search)->orderBy('nombre', 'asc')->get();

        return view('admin.generos.index', compact('generos'));
    }

    public function directores(Request $request)
    {
        $directores = Director::search($request->search)->orderBy('nombre', 'asc')->get();

        return view('admin.directores.index', compact('directores'));
    }

    public function actores(Request $request)
    {
        $actores = Actor::search($request->search)->orderBy('nombre', 'asc')->get();

        return view('admin.actores.index', compact('actores'));
    }

    public function premios(Request $request)
    {
        $premios = Premio::search($request->search)->orderBy('anio', 'desc')
            ->orderBy('nombre', 'asc')->get();

        return view('admin.premios.index', compact('premios'));
    }

    public function resenas()
    {
        $peliculas = Pelicula::has('resenas')->withCount('resenas')->get();

        return view('admin.resenas.index', compact('peliculas'));
    }

    public function resenasPorPelicula($peliculaId)
    {
        $pelicula = Pelicula::with('resenas.user')->findOrFail($peliculaId);

        return view('admin.resenas.show', compact('pelicula'));
    }

}
