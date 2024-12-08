<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;
use App\Models\Resena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ResenaController extends Controller
{

    public function index($peliculaId)
    {
        $pelicula = Pelicula::with('resenas.user')->findOrFail($peliculaId);

        return view('peliculas.resenas', compact('pelicula'));
    }



    public function store(Request $request, $peliculaId)
    {
        $validated = $request->validate([
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ]);

        if (Resena::where('user_id', auth()->id())->where('pelicula_id', $peliculaId)->exists()) {
            return redirect()->back()->withErrors([
                'error' => 'Ya has dejado una reseña para esta película. Borra la hayas dejado para poner una nueva.'
            ]);
        }

        Resena::create([
            'user_id' => auth()->id(),
            'pelicula_id' => $peliculaId,
            'calificacion' => $validated['calificacion'],
            'comentario' => $validated['comentario'],
        ]);


        Cache::forget("pelicula_{$peliculaId}_promedio_calificacion");

        return redirect()->back()->with('success', 'Reseña añadida correctamente.');
    }

    public function destroy($id)
    {
        $resena = Resena::findOrFail($id);

        if ($resena->user_id !== auth()->id() && auth()->user()->role !== 'ADMIN') {
            abort(403, 'No estas autorizado para realizar esta acción.');
        }

        $peliculaId = $resena->pelicula_id;

        $resena->delete();

        $promedio = Resena::where('pelicula_id', $peliculaId)->avg('calificacion');


        Cache::put("pelicula_{$peliculaId}_promedio_calificacion", $promedio, now()->addMinutes(10));


        return redirect()->back()->with('success', 'Reseña eliminada correctamente.');
    }
}
