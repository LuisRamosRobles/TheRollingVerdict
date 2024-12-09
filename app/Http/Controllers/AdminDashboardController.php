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
    /**
     * @OA\Get(
     *     path="/admin/dashboard",
     *     summary="Mostrar el panel de control del administrador",
     *     description="Devuelve la vista principal del panel de control del administrador.",
     *     operationId="getAdminDashboard",
     *     tags={"Admin Dashboard"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Vista del panel de administrador cargada con éxito"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado (Debe iniciar sesión)"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado (Usuario no autorizado para realizar esta acción)"
     *     )
     * )
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * @OA\Get(
     *     path="/admin/peliculas",
     *     summary="Listar todas las películas",
     *     description="Devuelve una lista de todas las películas.",
     *     operationId="getAdminPeliculas",
     *     tags={"Admin Dashboard"},
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Texto para buscar películas por título.",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de películas obtenida con éxito",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Pelicula")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acción no autorizada"
     *     )
     * )
     */
    public function peliculas(Request $request)
    {
        $peliculas = Pelicula::search($request->search)->orderBy('estreno', 'desc')->get();

        return view('admin.peliculas.index', compact('peliculas'));
    }

    /**
     * @OA\Get(
     *     path="/admin/generos",
     *     summary="Listar todos los géneros",
     *     description="Devuelve una lista de todos los géneros.",
     *     operationId="getAdminGeneros",
     *     tags={"Admin Dashboard"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de géneros obtenida con éxito",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Genero")
     *         )
     *     )
     * )
     */
    public function generos(Request $request)
    {
        $generos = Genero::search($request->search)->orderBy('nombre', 'asc')->get();

        return view('admin.generos.index', compact('generos'));
    }

    /**
     * @OA\Get(
     *     path="/admin/directores",
     *     summary="Listar todos los directores",
     *     description="Devuelve una lista de todos los directores.",
     *     operationId="getAdminDirectores",
     *     tags={"Admin Dashboard"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de directores obtenida con éxito",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Director")
     *         )
     *     )
     * )
     */
    public function directores(Request $request)
    {
        $directores = Director::search($request->search)->orderBy('nombre', 'asc')->get();

        return view('admin.directores.index', compact('directores'));
    }

    /**
     * @OA\Get(
     *     path="/admin/actores",
     *     summary="Listar todos los actores",
     *     description="Devuelve una lista de todos los actores.",
     *     operationId="getAdminActores",
     *     tags={"Admin Dashboard"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de actores obtenida con éxito",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Actor")
     *         )
     *     )
     * )
     */
    public function actores(Request $request)
    {
        $actores = Actor::search($request->search)->orderBy('nombre', 'asc')->get();

        return view('admin.actores.index', compact('actores'));
    }

    /**
     * @OA\Get(
     *     path="/admin/premios",
     *     summary="Listar todos los premios",
     *     description="Devuelve una lista de todos los premios.",
     *     operationId="getAdminPremios",
     *     tags={"Admin Dashboard"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de premios obtenida con éxito",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Premio")
     *         )
     *     )
     * )
     */
    public function premios(Request $request)
    {
        $premios = Premio::search($request->search)->orderBy('anio', 'desc')
            ->orderBy('nombre', 'asc')->get();

        return view('admin.premios.index', compact('premios'));
    }

    /**
     * @OA\Get(
     *     path="/resenas",
     *     summary="Listar películas con reseñas",
     *     description="Devuelve una lista de películas que tienen reseñas, incluyendo el conteo de reseñas, desde el panel de administración.",
     *     operationId="getAdminPeliculasConResenas",
     *     tags={"Admin Dashboard"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de películas con reseñas obtenida con éxito",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", description="ID de la película"),
     *                 @OA\Property(property="titulo", type="string", description="Título de la película"),
     *                 @OA\Property(property="resenas_count", type="integer", description="Cantidad de reseñas asociadas a la película")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado (Debe iniciar sesión)"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acción no autorizada (Solo accesible para administradores)"
     *     )
     * )
     */

    public function resenas()
    {
        $peliculas = Pelicula::has('resenas')->withCount('resenas')->get();

        return view('admin.resenas.index', compact('peliculas'));
    }

    /**
     * @OA\Get(
     *     path="/resenas/{resenaId}",
     *     summary="Listar reseñas por película",
     *     description="Devuelve una lista de reseñas de una película específica.",
     *     operationId="getResenasPorPelicula",
     *     tags={"Admin Dashboard"},
     *     @OA\Parameter(
     *         name="peliculaId",
     *         in="path",
     *         description="ID único de la película",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de reseñas obtenida con éxito",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Resena")
     *         )
     *     )
     * )
     */
    public function resenasPorPelicula($peliculaId)
    {
        $pelicula = Pelicula::with('resenas.user')->findOrFail($peliculaId);

        return view('admin.resenas.show', compact('pelicula'));
    }

}
