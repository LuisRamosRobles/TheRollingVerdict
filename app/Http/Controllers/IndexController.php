<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index()
    {
        $peliculas = Pelicula::with('resenas')
                               ->get()
                               ->sortByDesc(function ($pelicula) {
                                    return $pelicula->promedio_calificacion;
                               })
                               ->take(4);

        $usuario = Auth::user();


        return view('index', compact('peliculas', 'usuario'));
    }
}
