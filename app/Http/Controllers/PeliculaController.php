<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\Director;
use App\Models\Genero;
use App\Models\Pelicula;
use App\Models\Premio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class PeliculaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/peliculas",
     *     summary="Obtener una lista de películas",
     *     description="Devuelve una lista paginada de películas ordenadas por fecha de estreno, con opción de búsqueda.",
     *     operationId="getPeliculas",
     *     tags={"Peliculas"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Cadena de texto para buscar películas por título o descripción.",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de la página para la paginación.",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de películas obtenida con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", description="Página actual."),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Pelicula")),
     *             @OA\Property(property="total", type="integer", description="Número total de elementos."),
     *             @OA\Property(property="last_page", type="integer", description="Última página disponible.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor"
     *     )
     * )
     */

    public function index(Request $request)
    {
        $peliculas = Pelicula::search($request->search)->orderBy('estreno', 'desc')->paginate(4);

        return view('peliculas.index', compact('peliculas'));
    }

    /**
     * @OA\Get(
     *     path="/peliculas/{id}",
     *     summary="Obtener detalles de una película",
     *     description="Devuelve la información completa de una película específica junto con sus géneros, director, premios y actores.",
     *     operationId="getPelicula",
     *     tags={"Peliculas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único de la película",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="referer",
     *         in="query",
     *         description="URL de referencia opcional para redirigir después de la vista",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             format="url"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles de la película obtenidos con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", description="ID único de la película"),
     *             @OA\Property(property="titulo", type="string", description="Título de la película"),
     *             @OA\Property(property="descripcion", type="string", description="Descripción de la película"),
     *             @OA\Property(property="estreno", type="string", format="date", description="Fecha de estreno"),
     *             @OA\Property(property="generos", type="array", @OA\Items(ref="#/components/schemas/Genero")),
     *             @OA\Property(property="director", ref="#/components/schemas/Director"),
     *             @OA\Property(property="premios", type="array", @OA\Items(ref="#/components/schemas/Premio")),
     *             @OA\Property(property="actores", type="array", @OA\Items(ref="#/components/schemas/Actor"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Película no encontrada"
     *     )
     * )
     */

    public function show($id, Request $request)
    {
        $pelicula = Pelicula::with(['generos', 'director', 'premios', 'actores'])->findOrFail($id);
        $referer = $request->input('referer', route('peliculas.index'));

        return view('peliculas.show', compact('pelicula', 'referer'));
    }

    /**
     * @OA\Get(
     *     path="/peliculas/create",
     *     summary="Mostrar formulario para crear una nueva película",
     *     description="Devuelve las opciones necesarias (géneros, directores y actores activos) para crear una nueva película. Solo accesible para usuarios con rol ADMIN.",
     *     operationId="getPeliculaCreate",
     *     tags={"Peliculas"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Formulario cargado con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="generos", type="array", @OA\Items(ref="#/components/schemas/Genero")),
     *             @OA\Property(property="directores", type="array", @OA\Items(ref="#/components/schemas/Director")),
     *             @OA\Property(property="actores", type="array", @OA\Items(ref="#/components/schemas/Actor"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado (Usuario no autorizado para realizar esta acción)"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado (Debe iniciar sesión)"
     *     )
     * )
     */

    public function create()
    {
        $pelicula = new Pelicula();
        $generos = Genero::orderBy('nombre', 'asc')->get();
        $directores = Director::where('activo', true)->orderBy('nombre', 'asc')->get();
        $actores = Actor::where('activo', true)->orderBy('nombre', 'asc')->get();

        return view('peliculas.create', compact('pelicula', 'generos', 'directores', 'actores'));

    }

    /**
     * @OA\Post(
     *     path="/peliculas/store",
     *     summary="Crear una nueva película",
     *     description="Permite crear un registro de película junto con sus géneros, reparto, y otros detalles. Solo accesible para usuarios con rol ADMIN.",
     *     operationId="createPelicula",
     *     tags={"Peliculas"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"titulo", "generos", "estreno", "director_id", "sinopsis"},
     *             @OA\Property(property="titulo", type="string", description="Título de la película", example="Inception"),
     *             @OA\Property(property="generos", type="array", description="IDs de géneros asociados",
     *                 @OA\Items(type="integer", example=1)
     *             ),
     *             @OA\Property(property="estreno", type="string", format="date", description="Fecha de estreno (YYYY-MM-DD)", example="2023-12-01"),
     *             @OA\Property(property="director_id", type="integer", description="ID del director", example=2),
     *             @OA\Property(property="sinopsis", type="string", description="Sinopsis de la película", example="Un ladrón que roba secretos corporativos usando tecnología de sueños..."),
     *             @OA\Property(property="reparto", type="array", description="IDs de actores asociados",
     *                 @OA\Items(type="integer", example=3)
     *             ),
     *             @OA\Property(property="imagen", type="string", format="binary", description="Archivo de imagen de la película (opcional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Película creada con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Pelicula")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación de los datos"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado (Usuario no autorizado para realizar esta acción)"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado (Debe iniciar sesión)"
     *     )
     * )
     */

    public function store(Request $request)
    {


        $request->validate([
            'titulo' => 'required|min:1|max:120',
            'generos' => 'required|array',
            'generos.*' => 'exists:generos,id',
            'estreno' => 'required|date|date_format:Y-m-d|before_or_equal:today',
            'director_id' => 'required|exists:directores,id',
            'sinopsis' => 'required|min:5|max:255',
            'reparto' => 'array',
            'reparto.*' => 'exists:actores,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], $this->mensajes());


        try {

            $director = Director::find($request->input('director_id'));
            $inicioActividadYear = $director->inicio_actividad
                ? Carbon::parse($director->inicio_actividad)->year
                : null;

            $anioEstreno = $request->filled('estreno')
                ? Carbon::parse($request->input('estreno'))->year
                : null;

            $anioSiguienteEstreno = $request->filled('estreno')
                ? Carbon::parse($request->input('estreno'))->addYear()->year
                : null;

            if ($anioEstreno && $inicioActividadYear && $anioEstreno < $inicioActividadYear) {
                return redirect()->back()->withErrors([
                    'estreno' => "La fecha de estreno ($anioEstreno) no puede ser anterior al inicio de actividad del director ($inicioActividadYear)."
                ])->withInput();
            }




            $pelicula = Pelicula::create($request->except(['imagen', 'generos', 'reparto']));

            if ($request->hasFile('imagen')){
                $imagen = $request->file('imagen');
                $extension = $imagen->getClientOriginalExtension();
                $fileToSave = $pelicula->id . '.' .$extension;
                $pelicula->imagen = $imagen->storeAs('peliculas', $fileToSave, 'public');
            }


            if ($request->has('generos')){
                $pelicula->generos()->sync($request->input('generos'));
            }

            if ($request->has('reparto')) {
                $pelicula->actores()->sync($request->input('reparto'));
            }

            $pelicula->save();

            return redirect()->route('peliculas.show', $pelicula->id)->with('success', 'Película creada con éxito.');
        } catch (\Exception $e) {

            return redirect()->back()->withErrors(['error' => 'Error al crear la película.' . $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/peliculas/{id}/edit",
     *     summary="Obtener los datos necesarios para editar una película",
     *     description="Devuelve la información de una película específica junto con sus géneros, director, reparto seleccionado y actores disponibles para editar. Solo accesible para usuarios con rol ADMIN.",
     *     operationId="editPelicula",
     *     tags={"Peliculas"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único de la película",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Datos de la película obtenidos con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="pelicula", ref="#/components/schemas/Pelicula"),
     *             @OA\Property(property="generos", type="array", description="Lista de géneros disponibles",
     *                 @OA\Items(ref="#/components/schemas/Genero")
     *             ),
     *             @OA\Property(property="directores", type="array", description="Lista de directores activos disponibles",
     *                 @OA\Items(ref="#/components/schemas/Director")
     *             ),
     *             @OA\Property(property="repartoSeleccionado", type="array", description="Lista de actores seleccionados para la película",
     *                 @OA\Items(ref="#/components/schemas/Actor")
     *             ),
     *             @OA\Property(property="actoresDisponibles", type="array", description="Lista de actores disponibles para agregar al reparto",
     *                 @OA\Items(ref="#/components/schemas/Actor")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado (Usuario no autorizado para realizar esta acción)"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado (Debe iniciar sesión)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Película no encontrada"
     *     )
     * )
     */

    public function edit($id)
    {
        $pelicula = Pelicula::with(['generos', 'director', 'actores'])->findOrFail($id);
        $generos = Genero::orderBy('nombre', 'asc')->get();
        $directores = Director::where('activo', true)->get();


        $repartoSeleccionado = $pelicula->actores;


        $actoresDisponibles = Actor::where('activo', true)
            ->whereNotIn('id', $repartoSeleccionado->pluck('id'))
            ->orderBy('nombre', 'asc')
            ->get();

        return view('peliculas.edit', compact(
            'pelicula',
            'generos',
            'directores',
            'repartoSeleccionado',
            'actoresDisponibles'));
    }

    /**
     * @OA\Put(
     *     path="/peliculas/{id}",
     *     summary="Actualizar una película existente",
     *     description="Actualiza los detalles de una película, incluyendo géneros, reparto, y otros datos. Solo accesible para usuarios con rol ADMIN.",
     *     operationId="updatePelicula",
     *     tags={"Peliculas"},
     *
     *     @OA\Parameter(
     *         name="id",
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
     *             required={"titulo", "generos", "estreno", "director_id", "sinopsis", "reparto"},
     *             @OA\Property(property="titulo", type="string", description="Título de la película", example="The Dark Knight"),
     *             @OA\Property(property="generos", type="array", description="IDs de géneros asociados",
     *                 @OA\Items(type="integer", example=1)
     *             ),
     *             @OA\Property(property="estreno", type="string", format="date", description="Fecha de estreno (YYYY-MM-DD)", example="2008-07-18"),
     *             @OA\Property(property="director_id", type="integer", description="ID del director", example=3),
     *             @OA\Property(property="sinopsis", type="string", description="Sinopsis de la película", example="Un caballero oscuro protege Gotham de un caótico villano conocido como el Joker."),
     *             @OA\Property(property="reparto", type="array", description="IDs de actores asociados",
     *                 @OA\Items(type="integer", example=5)
     *             ),
     *             @OA\Property(property="imagen", type="string", format="binary", description="Archivo de imagen de la película (opcional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Película actualizada con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Pelicula")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error en la validación de los datos"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado (Usuario no autorizado para realizar esta acción)"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado (Debe iniciar sesión)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Película no encontrada"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'titulo' => 'required|min:1|max:120',
            'generos' => 'required|array',
            'generos.*' => 'exists:generos,id',
            'estreno' => 'required|date|date_format:Y-m-d|before_or_equal:today',
            'director_id' => 'required|exists:directores,id',
            'sinopsis' => 'required|min:5|max:255',
            'reparto' => 'required|array',
            'reparto.*' => 'exists:actores,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], $this->mensajes());

        try{
            $pelicula = Pelicula::with(['generos', 'director', 'actores'])->findOrFail($id);

            $director = Director::findOrFail($request->input('director_id'));
            $inicioActividadYear = $director->inicio_actividad
                ? $director->inicio_actividad
                : null;

            $anioEstreno = $request->filled('estreno')
                ? Carbon::parse($request->input('estreno'))->year
                : null;

            $anioSiguienteEstreno = $request->filled('estreno')
                ? Carbon::parse($request->input('estreno'))->addYear()->year
                : null;

            if ($anioEstreno && $inicioActividadYear && $anioEstreno < $inicioActividadYear) {
                return redirect()->back()->withErrors([
                    'estreno' => "La fecha de estreno ($anioEstreno) no puede ser anterior al inicio de actividad del director ($inicioActividadYear)."
                ])->withInput();
            }



            $pelicula->update($request->except('imagen', 'generos','reparto'));

            if ($request->has('reparto')) {
                $pelicula->actores()->sync($request->input('reparto'));
            }


            if ($request->hasFile('imagen')) {
                if ($pelicula->imagen != Pelicula::$IMAGEN_DEFAULT && Storage::exists($pelicula->imagen)) {
                    Storage::delete($pelicula->imagen);
                }

                $imagen = $request->file('imagen');
                $extension = $imagen->getClientOriginalExtension();
                $fileToSave = $pelicula->id. '.'. $extension;
                $pelicula->imagen = $imagen->storeAs('peliculas', $fileToSave, 'public');
            }

            if ($request->has('generos')){
                $pelicula->generos()->sync($request->input('generos'));
            }

            $pelicula->save();
            return redirect()->route('peliculas.show', $pelicula->id)->with('success', 'Película actualizada con éxito.');
        } catch (\Exception $e){
            return redirect()->back()->withErrors(['error' => 'Error al actualizar la película.' . $e->getMessage()]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/peliculas/{id}",
     *     summary="Eliminar una película",
     *     description="Elimina una película específica de forma lógica (soft delete). Solo accesible para usuarios con rol ADMIN.",
     *     operationId="deletePelicula",
     *     tags={"Peliculas"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único de la película",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Película eliminada con éxito"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado (Usuario no autorizado para realizar esta acción)"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado (Debe iniciar sesión)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Película no encontrada"
     *     )
     * )
     */

    public function destroy($id)
    {
        try {
            $pelicula = Pelicula::findOrFail($id);

            $pelicula->delete();

            return redirect()->route('admin.peliculas')->with('success', 'Película eliminada con éxito.');
        }catch (\Exception $e){
            return redirect()->back()->withErrors(['error' => 'Error al eliminar la película.']);
        }
    }

    /**
     * @OA\Get(
     *     path="/peliculas/eliminados",
     *     summary="Listar películas eliminadas",
     *     description="Obtiene una lista paginada de películas que han sido eliminadas lógicamente. Solo accesible para usuarios con rol ADMIN.",
     *     operationId="getDeletedPeliculas",
     *     tags={"Peliculas"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de películas eliminadas obtenida con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", description="Página actual."),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Pelicula")),
     *             @OA\Property(property="total", type="integer", description="Número total de elementos."),
     *             @OA\Property(property="last_page", type="integer", description="Última página disponible.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado (Usuario no autorizado para realizar esta acción)"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado (Debe iniciar sesión)"
     *     )
     * )
     */

    public function deleted()
    {
        $peliculas = Pelicula::onlyTrashed()->paginate(4);

        return view('peliculas.deleted', compact('peliculas'));
    }

    /**
     * @OA\Patch(
     *     path="/peliculas/{id}/restaurar",
     *     summary="Restaurar una película eliminada",
     *     description="Restaura una película específica que fue eliminada lógicamente. Solo accesible para usuarios con rol ADMIN.",
     *     operationId="restorePelicula",
     *     tags={"Peliculas"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único de la película",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Película restaurada con éxito"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado (Usuario no autorizado para realizar esta acción)"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado (Debe iniciar sesión)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Película no encontrada"
     *     )
     * )
     */

    public function restore($id)
    {
        try{
            $pelicula = Pelicula::onlyTrashed()->findOrFail($id);
            $pelicula->restore();

            return redirect()->route('peliculas.deleted')->with('success', 'Película restaurada con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al restaurar la película.']);
        }
    }

    public function mensajes()
    {
        return [
            'titulo.required' => 'El campo título de la película es obligatorio.',
            'titulo.min' => 'El campo título debe tener al menos un caracteres.',
            'titulo.max' => 'El campo título no puede superar los 120 caracteres.',

            'generos.required' => 'Debe seleccionar al menos un género.',
            'generos.*.exists' => 'El género seleccionado no es válido.',

            'estreno.required' => 'El campo fecha de estreno es obligatorio.',
            'estreno.date' => 'El campo fecha de estreno debe ser una fecha válida.',
            'estreno.date_format' => 'El campo fecha de estreno debe tener el formato AAAA-MM-DD.',
            'estreno.before_or_equal' => 'La fecha de estreno no puede ser futura.',

            'director_id.required' => 'El campo director es obligatorio.',
            'director_id.exists' => 'El director seleccionado no es válido.',

            'sinopsis.required' => 'El campo sinopsis es obligatorio.',
            'sinopsis.min' => 'El campo sinopsis debe tener al menos 5 caracteres.',
            'sinopsis.max' => 'El campo sinopsis no puede superar los 120 caracteres.',

            'reparto.required' => 'Debe seleccionar al menos un actor.',
            'reparto.*.exists' => 'El actor seleccionado no es válido.',

            'imagen.required' => 'Debes seleccionar una imagen.',
            'imagen.image' => 'El archivo seleccionado no es una imagen.',
            'imagen.mimes' => 'El archivo seleccionado debe ser una imagen en formato jpeg, png, jpg, gif o svg.',
            'imagen.max' => 'El tamaño máximo de la imagen es de 2MB.',
        ];

    }
}
