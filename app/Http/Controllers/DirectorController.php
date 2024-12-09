<?php

namespace App\Http\Controllers;

use App\Models\Director;
use Carbon\Carbon;
use Illuminate\Http\Request;


class DirectorController extends Controller
{

    /**
     * @OA\Get(
     *     path="/directores",
     *     summary="Obtener una lista de directores",
     *     description="Devuelve una lista paginada de directores ordenados alfabéticamente, con opción de búsqueda por nombre.",
     *     operationId="getDirectores",
     *     tags={"Directores"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Cadena de texto para buscar directores por nombre.",
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
     *         description="Lista de directores obtenida con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", description="Página actual."),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Director")),
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
        $directores = Director::search($request->search)->orderBy('nombre', 'asc')->paginate(4);

        return view('directores.index', compact('directores'));
    }

    /**
     * @OA\Get(
     *     path="/directores/{id}",
     *     summary="Obtener detalles de un director",
     *     description="Devuelve la información completa de un director específico, junto con una lista paginada de películas y premios asociados.",
     *     operationId="getDirector",
     *     tags={"Directores"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del director",
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
     *         description="Detalles del director obtenidos con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", description="ID único del director"),
     *             @OA\Property(property="nombre", type="string", description="Nombre del director"),
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
     *         description="Director no encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor"
     *     )
     * )
     */

    public function show($id, Request $request)
    {
        $director = Director::with(['peliculas', 'premios'])->findOrFail($id);
        $referer = $request->input('referer', route('directores.index'));
        $peliculas = $director->peliculas()->paginate(5);

        return view('directores.show', compact('director', 'peliculas', 'referer'));
    }

    /**
     * @OA\Get(
     *     path="/directores/create",
     *     summary="Mostrar formulario para crear un director",
     *     description="Devuelve el formulario necesario para crear un nuevo director. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="createDirector",
     *     tags={"Directores"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Formulario cargado con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="director", ref="#/components/schemas/Director")
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
        $director = new Director();

        return view('directores.create', compact('director'));
    }

    /**
     * @OA\Post(
     *     path="/directores/store",
     *     summary="Crear un nuevo director",
     *     description="Crea un nuevo director y guarda los datos enviados, incluida una imagen opcional. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="storeDirector",
     *     tags={"Directores"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"nombre"},
     *             @OA\Property(property="nombre", type="string", description="Nombre del director", example="Christopher Nolan"),
     *             @OA\Property(property="fecha_nac", type="string", format="date", description="Fecha de nacimiento del director (YYYY-MM-DD)", example="1970-07-30"),
     *             @OA\Property(property="lugar_nac", type="string", description="Lugar de nacimiento del director", example="Londres"),
     *             @OA\Property(property="biografia", type="string", description="Breve biografía del director", example="Director conocido por películas como Inception."),
     *             @OA\Property(property="inicio_actividad", type="integer", description="Año de inicio de actividad del director", example=1995),
     *             @OA\Property(property="fin_actividad", type="integer", description="Año de fin de actividad del director", example=2023),
     *             @OA\Property(property="activo", type="boolean", description="Estado del director (activo o inactivo)", example=true),
     *             @OA\Property(property="imagen", type="string", format="binary", description="Archivo de imagen del director (opcional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Director creado con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Director")
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
            'nombre' => 'required|string|min:3|max:120|regex:/^[a-zA-Z\s]+$/',
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


            if ($inicioActividadYear && $nacimientoYear && $inicioActividadYear < $nacimientoYear) {
                return redirect()->back()->withErrors([
                    'inicio_actividad' => 'La fecha de inicio de actividad no puede ser anterior a la fecha de nacimiento.'
                ])->withInput();
            }

            $data = $request->except(['imagen']);
            $data['fecha_nac'] = $request->filled('fecha_nac') ? $request->input('fecha_nac') : null;
            $data['inicio_actividad'] = $request->filled('inicio_actividad') ? $request->input('inicio_actividad') : null;
            $data['fin_actividad'] = $request->filled('fin_actividad')? $request->input('fin_actividad') : null;

            $director = Director::create($data);

            if ($request->hasFile('imagen')) {
                $director->imagen = $this->procesarImagen($director, $request->file('imagen'));
            }

            $director->save();
            return redirect()->route('directores.show', $director->id)->with('success', 'Director creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Error al crear el director.' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * @OA\Get(
     *     path="/directores/{id}/edit",
     *     summary="Mostrar formulario para editar un director",
     *     description="Devuelve el formulario necesario para editar un director existente. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="editDirector",
     *     tags={"Directores"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del director",
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
     *             @OA\Property(property="director", ref="#/components/schemas/Director")
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
     *         description="Director no encontrado"
     *     )
     * )
     */

    public function edit($id)
    {
        $director = Director::findOrFail($id);

        return view('directores.edit', compact('director'));
    }

    /**
     * @OA\Patch(
     *     path="/directores/{id}",
     *     summary="Actualizar un director existente",
     *     description="Actualiza los datos de un director existente, incluida una imagen opcional. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="updateDirector",
     *     tags={"Directores"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del director",
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
     *             @OA\Property(property="nombre", type="string", description="Nombre del director", example="Steven Spielberg"),
     *             @OA\Property(property="fecha_nac", type="string", format="date", description="Fecha de nacimiento del director (YYYY-MM-DD)", example="1946-12-18"),
     *             @OA\Property(property="lugar_nac", type="string", description="Lugar de nacimiento del director", example="Cincinnati"),
     *             @OA\Property(property="biografia", type="string", description="Breve biografía del director", example="Director conocido por películas como Jurassic Park."),
     *             @OA\Property(property="inicio_actividad", type="integer", description="Año de inicio de actividad del director", example=1971),
     *             @OA\Property(property="fin_actividad", type="integer", description="Año de fin de actividad del director", example=2023),
     *             @OA\Property(property="activo", type="boolean", description="Estado del director (activo o inactivo)", example=true),
     *             @OA\Property(property="imagen", type="string", format="binary", description="Archivo de imagen del director (opcional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Director actualizado con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Director")
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
     *         description="Director no encontrado"
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|min:3|max:120',
            'fecha_nac' => 'nullable|date|date_format:Y-m-d|before:' .now(),
            'lugar_nac' => 'nullable|string|max:120',
            'biografia' => 'nullable|max:255',
            'inicio_actividad' => 'nullable|integer|digits:4|before_or_equal:' . now()->year,
            'fin_actividad' => 'nullable|integer|digits:4|before_or_equal:' . now()->year,
            'activo' => 'boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], $this->mensajes());

        try {
            $director = Director::findOrFail($id);

            $nacimientoYear = $request->filled('fecha_nac')
                ? Carbon::parse($request->input('fecha_nac'))->year
                : ($director->fecha_nac ? Carbon::parse($director->fecha_nac)->year : null);

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


            $director->update($request->except('imagen'));





            if ($request->hasFile('imagen')) {

                $director->imagen = $this->procesarImagen($director, $request->file('imagen'));
            }

            $director->save();
            return redirect()->route('directores.show', $director->id)->with('success', 'Director actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Error al actualizar el director.' . $e->getMessage(),
            ])->withInput();
        }
    }

    /**
     * @OA\Delete(
     *     path="/directores/{id}",
     *     summary="Eliminar un director",
     *     description="Elimina un director específico de forma lógica (soft delete). Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="deleteDirector",
     *     tags={"Directores"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del director",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Director eliminado correctamente"
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
     *         description="Director no encontrado"
     *     )
     * )
     */

    public function destroy($id)
    {
        try {
            $director = Director::findOrFail($id);

            $director->delete();
            return redirect()->route('admin.directores')->with('success', 'Director eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar el director.']);
        }
    }

    /**
     * @OA\Get(
     *     path="/directores/eliminados",
     *     summary="Listar directores eliminados",
     *     description="Obtiene una lista paginada de directores que han sido eliminados lógicamente. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="getDeletedDirectores",
     *     tags={"Directores"},
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
     *         description="Lista de directores eliminados obtenida con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", description="Página actual."),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Director")),
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
        $directores = Director::onlyTrashed()->paginate(4);

        return view('directores.deleted', compact('directores'));
    }

    /**
     * @OA\Patch(
     *     path="/directores/{id}/restaurar",
     *     summary="Restaurar un director eliminado",
     *     description="Restaura un director específico que fue eliminado lógicamente. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="restoreDirector",
     *     tags={"Directores"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del director",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Director restaurado correctamente"
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
     *         description="Director no encontrado"
     *     )
     * )
     */

    public function restore($id)
    {
        try {
            $director = Director::onlyTrashed()->findOrFail($id);
            $director->restore();

            return redirect()->route('directores.deleted')->with('success', 'Director restaurado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al restaurar el director.']);
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
     * Valida las fechas relacionadas con un director y devuelve errores específicos.
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
     * Procesa y guarda una imagen asociada a un director.
     *
     * @param Director $director Instancia del director.
     * @param UploadedFile $imagen Archivo de imagen cargado.
     * @return string Ruta donde se almacenó la imagen.
     */

    private function procesarImagen($director, $imagen)
    {

        $extension = $imagen->getClientOriginalExtension();
        $fileToSave = $director->id . '.' . $extension;

        return $imagen->storeAs('directores', $fileToSave, 'public');
    }




}
