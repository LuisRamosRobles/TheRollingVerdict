<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;
use App\Models\Resena;
use Illuminate\Http\Request;

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

        Resena::create([
            'user_id' => auth()->id(),
            'pelicula_id' => $peliculaId,
            'calificacion' => $validated['calificacion'],
            'comentario' => $validated['comentario'],
        ]);

        return redirect()->back()->with('success', 'Reseña añadida correctamente.');
    }

    public function destroy($id)
    {
        $resena = Resena::findOrFail($id);

        if ($resena->user_id !== auth()->id()) {
            abort(403, 'No estas autorizado para realizar esta acción.');
        }

        $resena->delete();

        return redirect()->back()->with('success', 'Reseña eliminada correctamente.');
    }
}
