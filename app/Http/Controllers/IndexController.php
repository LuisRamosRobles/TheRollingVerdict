<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    /**
     * @OA\Get(
     *     path="/",
     *     summary="Obtener las películas mejor calificadas",
     *     description="Devuelve una lista de las 4 películas con mejor promedio de calificación junto con la información del usuario autenticado.",
     *     operationId="getInicio",
     *     tags={"Inicio"},
     *     @OA\Response(
     *         response=200,
     *         description="Página principal cargada con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="peliculas",
     *                 type="array",
     *                 description="Las 4 películas con mejor calificación",
     *                 @OA\Items(ref="#/components/schemas/Pelicula")
     *             ),
     *             @OA\Property(
     *                 property="usuario",
     *                 type="object",
     *                 description="Información del usuario autenticado",
     *                 @OA\Property(property="id", type="integer", description="ID del usuario"),
     *                 @OA\Property(property="name", type="string", description="Nombre del usuario"),
     *                 @OA\Property(property="email", type="string", description="Correo electrónico del usuario")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     )
     * )
     */
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
