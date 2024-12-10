<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\Pelicula;
use App\Models\Premio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ActorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/actores",
     *     summary="Obtener una lista de actores",
     *     description="Devuelve una lista paginada de actores ordenados alfabéticamente, con opción de búsqueda por nombre.",
     *     operationId="getActores",
     *     tags={"Actores"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Cadena de texto para buscar actores por nombre.",
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
     *         description="Lista de actores obtenida con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", description="Página actual."),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Actor")),
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
        $actores = Actor::search($request->search)->orderBy('nombre', 'asc')->paginate(4);

        return view('actores.index', compact('actores'));
    }

    /**
     * @OA\Get(
     *     path="/actores/{id}",
     *     summary="Obtener detalles de un actor",
     *     description="Devuelve la información completa de un actor específico, junto con una lista paginada de películas y premios asociados.",
     *     operationId="getActor",
     *     tags={"Actores"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del actor",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número de la página para la paginación de películas asociadas.",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del actor obtenidos con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", description="ID único del actor"),
     *             @OA\Property(property="nombre", type="string", description="Nombre del actor"),
     *             @OA\Property(property="peliculas", type="array", description="Lista de películas asociadas",
     *                 @OA\Items(ref="#/components/schemas/Pelicula")
     *             ),
     *             @OA\Property(property="premios", type="array", description="Lista de premios asociados",
     *                 @OA\Items(ref="#/components/schemas/Premio")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Actor no encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor"
     *     )
     * )
     */

    public function show($id, Request $request)
    {
        $actor = Actor::with(['peliculas', 'premios'])->findOrFail($id);
        $referer = $request->input('referer', route('actores.index'));
        $peliculas = $actor->peliculas()->paginate(5);

        return view('actores.show', compact('actor', 'peliculas', 'referer'));
    }

    /**
     * @OA\Get(
     *     path="/actores/create",
     *     summary="Mostrar formulario para crear un actor",
     *     description="Devuelve el formulario necesario para crear un nuevo actor. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="createActor",
     *     tags={"Actores"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Formulario cargado con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="actor", ref="#/components/schemas/Actor")
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
        $actor = new Actor();

        return view('actores.create', compact('actor'));
    }

    /**
     * @OA\Post(
     *     path="/actores/store",
     *     summary="Crear un nuevo actor",
     *     description="Crea un nuevo actor y guarda los datos enviados, incluida una imagen opcional. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="storeActor",
     *     tags={"Actores"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"nombre"},
     *             @OA\Property(property="nombre", type="string", description="Nombre del actor", example="Leonardo DiCaprio"),
     *             @OA\Property(property="fecha_nac", type="string", format="date", description="Fecha de nacimiento del actor (YYYY-MM-DD)", example="1974-11-11"),
     *             @OA\Property(property="lugar_nac", type="string", description="Lugar de nacimiento del actor", example="Los Ángeles"),
     *             @OA\Property(property="biografia", type="string", description="Breve biografía del actor", example="Actor ganador de un Óscar por The Revenant."),
     *             @OA\Property(property="inicio_actividad", type="integer", description="Año de inicio de actividad del actor", example=1990),
     *             @OA\Property(property="fin_actividad", type="integer", description="Año de fin de actividad del actor", example=2023),
     *             @OA\Property(property="activo", type="boolean", description="Estado del actor (activo o inactivo)", example=true),
     *             @OA\Property(property="imagen", type="string", format="binary", description="Archivo de imagen del actor (opcional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Actor creado con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Actor")
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
            'nombre' => 'required|string|min:3|max:120|regex:/^[a-zA-Z\s.]+$/',
            'fecha_nac' => 'nullable|date|date_format:Y-m-d|before:' . now(),
            'lugar_nac' => 'nullable|string|max:120',
            'biografia' => 'nullable|max:255',
            'inicio_actividad' => 'nullable|integer|digits:4|before_or_equal:' . now()->year,
            'fin_actividad' => 'nullable|integer|digits:4|before_or_equal:' . now()->year,
            'activo' => 'boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], $this->mensajes());

        try {

            $nacimientoYear = $request->filled('fecha_nac')
                ? Carbon::parse($request->input('fecha_nac'))->year
                : null;

            $inicioActividadYear = $request->filled('inicio_actividad')
                ? $request->input('inicio_actividad')
                : null;

            $finActividadYear = $request->filled('fin_actividad')
                ? $request->input('fin_actividad')
                : null;

            $erroresFechas = $this->validarFechas($nacimientoYear, $inicioActividadYear, $finActividadYear);
            if ($erroresFechas) {
                return redirect()->back()->withErrors($erroresFechas)->withInput();
            }

            $data = $request->except(['imagen']);
            $data['fecha_nac'] = $request->filled('fecha_nac') ? $request->input('fecha_nac') : null;
            $data['inicio_actividad'] = $request->filled('inicio_actividad') ? $request->input('inicio_actividad') : null;
            $data['fin_actividad'] = $request->filled('fin_actividad')? $request->input('fin_actividad') : null;

            $actor = Actor::create($data);

            if ($request->hasFile('imagen')) {
                $actor->imagen = $this->procesarImagen($actor, $request->file('imagen'));
            }

            $actor->save();
            return redirect()->route('actores.show', $actor->id)->with('success', 'Actor creado correctamente.');



        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Error al crear el actor.'. $e->getMessage(),
            ])->withInput();
        }
    }


    /**
     * @OA\Get(
     *     path="/actores/{id}/edit",
     *     summary="Mostrar formulario para editar un actor",
     *     description="Devuelve el formulario necesario para editar un actor existente. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="editActor",
     *     tags={"Actores"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del actor",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Formulario cargado con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="actor", ref="#/components/schemas/Actor"),
     *             @OA\Property(property="peliculas", type="array", description="Lista de películas disponibles para asociación",
     *                 @OA\Items(ref="#/components/schemas/Pelicula")
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
     *         description="Actor no encontrado"
     *     )
     * )
     */

    public function edit($id)
    {
        $actor = Actor::findOrFail($id);
        $peliculas = Pelicula::orderBy('titulo', 'asc')->get();

        return view('actores/edit', compact('actor', 'peliculas'));
    }

    /**
     * @OA\Patch(
     *     path="/actores/{id}",
     *     summary="Actualizar un actor existente",
     *     description="Actualiza los datos de un actor existente, incluida una imagen opcional. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="updateActor",
     *     tags={"Actores"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del actor",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"nombre"},
     *             @OA\Property(property="nombre", type="string", description="Nombre del actor", example="Morgan Freeman"),
     *             @OA\Property(property="fecha_nac", type="string", format="date", description="Fecha de nacimiento del actor (YYYY-MM-DD)", example="1937-06-01"),
     *             @OA\Property(property="lugar_nac", type="string", description="Lugar de nacimiento del actor", example="Memphis"),
     *             @OA\Property(property="biografia", type="string", description="Breve biografía del actor", example="Actor conocido por películas como Shawshank Redemption."),
     *             @OA\Property(property="inicio_actividad", type="integer", description="Año de inicio de actividad del actor", example=1955),
     *             @OA\Property(property="fin_actividad", type="integer", description="Año de fin de actividad del actor", example=2023),
     *             @OA\Property(property="activo", type="boolean", description="Estado del actor (activo o inactivo)", example=true),
     *             @OA\Property(property="imagen", type="string", format="binary", description="Archivo de imagen del actor (opcional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Actor actualizado con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Actor")
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
     *         description="Actor no encontrado"
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|min:3|max:120|regex:/^[a-zA-Z\s.]+$/',
            'fecha_nac' => 'nullable|date|date_format:Y-m-d|before:' . now(),
            'lugar_nac' => 'nullable|string|max:120',
            'biografia' => 'nullable|max:255',
            'inicio_actividad' => 'nullable|integer|digits:4|before_or_equal:' . now()->year,
            'fin_actividad' => 'nullable|integer|digits:4|before_or_equal:' . now()->year,
            'activo' => 'boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], $this->mensajes());

        try {
            $actor = Actor::findOrFail($id);

            $nacimientoYear = $request->filled('fecha_nac')
                ? Carbon::parse($request->input('fecha_nac'))->year
                : null;

            $inicioActividadYear = $request->filled('inicio_actividad')
                ? $request->input('inicio_actividad')
                : null;

            $finActividadYear = $request->filled('fin_actividad')
                ? $request->input('fin_actividad')
                : null;

            $erroresFechas = $this->validarFechas($nacimientoYear, $inicioActividadYear, $finActividadYear);
            if ($erroresFechas) {
                return redirect()->back()->withErrors($erroresFechas)->withInput();
            }



            $actor->update($request->except('imagen'));



            if ($request->hasFile('imagen')) {
                $actor->imagen = $this->procesarImagen($actor, $request->file('imagen'));
            }

            $actor->save();
            return redirect()->route('actores.show', $actor->id)->with('success', 'Actor editado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Error al actualizar el actor.' . $e->getMessage(),
            ])->withInput();
        }
    }

    /**
     * @OA\Delete(
     *     path="/actores/{id}",
     *     summary="Eliminar un actor",
     *     description="Elimina un actor específico de forma lógica (soft delete). Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="deleteActor",
     *     tags={"Actores"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del actor",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Actor eliminado correctamente"
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
     *         description="Actor no encontrado"
     *     )
     * )
     */

    public function destroy($id)
    {
        try {
            $actor = Actor::findOrFail($id);

            $actor->delete();
            return redirect()->route('admin.actores')->with('success', 'Actor eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Error al eliminar el actor.'
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/actores/eliminados",
     *     summary="Listar actores eliminados",
     *     description="Obtiene una lista paginada de actores que han sido eliminados lógicamente. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="getDeletedActores",
     *     tags={"Actores"},
     *
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
     *         description="Lista de actores eliminados obtenida con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", description="Página actual."),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Actor")),
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
        $actores = Actor::onlyTrashed()->paginate(4);

        return view('actores.deleted', compact('actores'));
    }

    /**
     * @OA\Patch(
     *     path="/actores/{id}/restaurar",
     *     summary="Restaurar un actor eliminado",
     *     description="Restaura un actor específico que fue eliminado lógicamente. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="restoreActor",
     *     tags={"Actores"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del actor",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Actor restaurado con éxito"
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
     *         description="Actor no encontrado"
     *     )
     * )
     */

    public function restore($id)
    {
        try {
            $actor = Actor::onlyTrashed()->findOrFail($id);
            $actor->restore();

            return redirect()->route('actores.deleted')->with('success', 'Actor restaurado con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Error al restaurar el actor.'
            ]);
        }
    }

    public function mensajes()
    {
        return [
            'nombre.required' => 'El campo nombre es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre debe tener máximo 120 caracteres',
            'nombre.regex' => 'El nombre debe solo puede contener letras y espacios',

            'fecha_nac.date' => 'El campo fecha de nacimiento debe ser una fecha válida',
            'fecha_nac.date_format' => 'El campo fecha de nacimiento debe tener el formato AAAA-MM-DD',
            'fecha_nac.before' => 'La fecha de nacimiento no puede ser posterior a la actual',

            'lugar_nac.max' => 'El lugar de nacimiento debe tener máximo 120 caracteres',

            'biografia.max' => 'La biografía debe tener máximo 255 caracteres',

            'inicio_actividad.integer' => 'El campo año de inicio de actividad debe ser una un número entero',
            'inicio_actividad.digits' => 'El campo año de inicio de actividad no puede tener más de 4 dígitos',
            'inicio_actividad.before_or_equal' => 'El año de inicio de actividad no puede ser posterior a la actual',

            'fin_actividad.integer' => 'El campo año de fin de actividad debe ser una un número entero',
            'fin_actividad.digits' => 'El campo año de fin de actividad no puede tener más de 4 dígitos',
            'fin_actividad.before_or_equal' => 'El año de fin de actividad no puede ser posterior a la actual',

            'activo.boolean' => 'El campo activo debe ser un valor booleano',

            'imagen.image' => 'El campo imagen debe ser una imagen válida',
            'imagen.mimes' => 'El campo imagen solo puede ser una imagen de tipo JPEG, PNG, JPG, GIF o SVG',
            'imagen.max' => 'El tamaño máximo de la imagen es de 2MB',
        ];
    }

    /**
     * Valida las fechas relacionadas con un actor y devuelve errores específicos.
     *
     * @param int|null $nacimientoYear Año de nacimiento.
     * @param int|null $inicioActividadYear Año de inicio de actividad.
     * @param int|null $finActividadYear Año de fin de actividad.
     * @return array|null Devuelve un array de errores o null si no hay errores.
     */

    public function validarFechas($nacimientoYear, $inicioActividadYear, $finActividadYear)
    {

        $errores = [];

        if ($inicioActividadYear && $nacimientoYear && $inicioActividadYear < $nacimientoYear) {
            $errores['inicio_actividad'] = 'La fecha de inicio de actividad no puede ser anterior a la fecha de nacimiento.';
        } elseif ($inicioActividadYear && $nacimientoYear && $inicioActividadYear == $nacimientoYear) {
            $errores['inicio_actividad'] = 'La fecha de inicio de actividad no puede ser igual a la fecha de nacimiento.';
        }

        if ($finActividadYear && $inicioActividadYear && $finActividadYear < $inicioActividadYear) {
            $errores['fin_actividad'] = 'El año de fin de actividad no puede ser anterior al año de inicio de actividad.';
        } elseif ($finActividadYear && $inicioActividadYear && $finActividadYear == $inicioActividadYear) {
            $errores['fin_actividad'] = 'El año de fin de actividad no puede ser igual al año de inicio de actividad.';
        } elseif ($finActividadYear && $nacimientoYear && $finActividadYear < $nacimientoYear) {
            $errores['fin_actividad'] = 'El año de fin de actividad no puede ser anterior a la fecha de nacimiento.';
        } elseif ($finActividadYear && $nacimientoYear && $finActividadYear == $nacimientoYear) {
            $errores['fin_actividad'] = 'El año de fin de actividad no puede ser igual a la fecha de nacimiento.';
        }

        return $errores ?: null;

    }

    /**
     * Procesa y guarda una imagen asociada a un actor.
     *
     * @param Actor $actor Instancia del actor.
     * @param UploadedFile $imagen Archivo de imagen cargado.
     * @return string Ruta donde se almacenó la imagen.
     */

    private function procesarImagen($actor, $imagen)
    {

        $extension = $imagen->getClientOriginalExtension();
        $fileToSave = $actor->id . '.' . $extension;

        return $imagen->storeAs('actores', $fileToSave, 'public');
    }
}
