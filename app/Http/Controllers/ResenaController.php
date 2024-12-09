<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;
use App\Models\Resena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ResenaController extends Controller
{

    /**
     * @OA\Get(
     *     path="/peliculas/{peliculaId}/resenas}",
     *     summary="Listar reseñas de una película",
     *     description="Devuelve una lista de reseñas asociadas a una película específica.",
     *     operationId="getResenas",
     *     tags={"Reseñas"},
     *     @OA\Parameter(
     *         name="peliculaId",
     *         in="path",
     *         description="ID único de la película",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de reseñas obtenida con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="pelicula", ref="#/components/schemas/Pelicula"),
     *             @OA\Property(property="resenas", type="array", description="Lista de reseñas",
     *                 @OA\Items(ref="#/components/schemas/Resena")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Película no encontrada"
     *     )
     * )
     */

    public function index($peliculaId)
    {
        $pelicula = Pelicula::with('resenas.user')->findOrFail($peliculaId);

        return view('peliculas.resenas', compact('pelicula'));
    }

    /**
     * @OA\Post(
     *     path="/peliculas/{peliculaId}/resenas",
     *     summary="Añadir una reseña a una película",
     *     description="Permite a un usuario autenticado añadir una reseña a una película específica.",
     *     operationId="storeResena",
     *     tags={"Reseñas"},
     *
     *     @OA\Parameter(
     *         name="peliculaId",
     *         in="path",
     *         description="ID único de la película",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"calificacion"},
     *             @OA\Property(property="calificacion", type="integer", description="Calificación del 1 al 5", example=4),
     *             @OA\Property(property="comentario", type="string", description="Comentario opcional sobre la película", example="Gran película con excelentes efectos especiales.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Reseña añadida con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Resena")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación de los datos"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Reseña duplicada (ya existe una reseña del usuario para esta película)"
     *     )
     * )
     */


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

    /**
     * @OA\Delete(
     *     path="/peliculas/resenas/{id}",
     *     summary="Eliminar una reseña",
     *     description="Permite a un usuario autenticado eliminar su propia reseña o a un administrador eliminar cualquier reseña.",
     *     operationId="deleteResena",
     *     tags={"Reseñas"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único de la reseña",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reseña eliminada con éxito"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acción no autorizada"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reseña no encontrada"
     *     )
     * )
     */


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
