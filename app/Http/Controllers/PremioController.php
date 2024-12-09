<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Models\Director;
use App\Models\Pelicula;
use App\Models\Premio;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PremioController extends Controller
{
    /**
     * @OA\Get(
     *     path="/premios",
     *     summary="Obtener una lista de premios",
     *     description="Devuelve una lista paginada de premios ordenados por año (descendente) y nombre (ascendente), con opción de búsqueda por nombre.",
     *     operationId="getPremios",
     *     tags={"Premios"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Cadena de texto para buscar premios por nombre.",
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
     *         description="Lista de premios obtenida con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", description="Página actual."),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Premio")),
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
        $premios = Premio::search($request->search)
            ->orderBy('anio', 'desc')
            ->orderBy('nombre', 'asc')
            ->paginate(4);

        return view('premios.index', compact('premios'));
    }

    /**
     * @OA\Get(
     *     path="/premios/{id}",
     *     summary="Obtener detalles de un premio",
     *     description="Devuelve la información completa de un premio específico.",
     *     operationId="getPremio",
     *     tags={"Premios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del premio",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del premio obtenidos con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", description="ID único del premio"),
     *             @OA\Property(property="nombre", type="string", description="Nombre del premio"),
     *             @OA\Property(property="anio", type="integer", description="Año en que se otorgó el premio"),
     *             @OA\Property(property="categoria", type="string", description="Categoría del premio"),
     *             @OA\Property(property="entidad", type="string", description="Entidad o persona que recibió el premio"),
     *             @OA\Property(property="descripcion", type="string", description="Descripción adicional del premio", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Premio no encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor"
     *     )
     * )
     */

    public function show($id)
    {
        $premio = Premio::findOrFail($id);

        return view('premios.show', compact('premio'));
    }

    /**
     * @OA\Get(
     *     path="/premios/create",
     *     summary="Mostrar formulario para crear un premio",
     *     description="Devuelve el formulario necesario para crear un nuevo premio. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="createPremio",
     *     tags={"Premios"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Formulario cargado con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="premio", ref="#/components/schemas/Premio"),
     *             @OA\Property(property="peliculas", type="array", description="Lista de películas disponibles",
     *                 @OA\Items(ref="#/components/schemas/Pelicula")
     *             ),
     *             @OA\Property(property="directores", type="array", description="Lista de directores disponibles",
     *                 @OA\Items(ref="#/components/schemas/Director")
     *             ),
     *             @OA\Property(property="actores", type="array", description="Lista de actores disponibles",
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
     *     )
     * )
     */

    public function create()
    {
        $premio = new Premio();
        $peliculas = Pelicula::with('actores')->get();
        $directores = Director::all();
        $actores = Actor::all();

        return view('premios.create', compact('premio','peliculas', 'directores', 'actores'));
    }

    /**
     * @OA\Post(
     *     path="/premios/store",
     *     summary="Crear un nuevo premio",
     *     description="Crea un nuevo premio y lo asocia a una película, director o actor. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="storePremio",
     *     tags={"Premios"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"nombre", "categoria", "anio", "entidad_type", "entidad_id"},
     *             @OA\Property(property="nombre", type="string", description="Nombre del premio", example="Óscar"),
     *             @OA\Property(property="categoria", type="string", description="Categoría del premio", example="Mejor Película"),
     *             @OA\Property(property="anio", type="integer", description="Año del premio", example=2023),
     *             @OA\Property(property="entidad_type", type="string", description="Tipo de entidad asociada al premio", example="App\Models\Pelicula"),
     *             @OA\Property(property="entidad_id", type="integer", description="ID de la entidad asociada al premio", example=1),
     *             @OA\Property(property="pelicula_id", type="integer", description="ID de la película asociada al premio (opcional)", nullable=true, example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Premio creado con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Premio")
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
            'nombre' => 'required|string',
            'categoria' => 'required|string',
            'anio' => 'required|integer|min:1900|max:' . now()->year,
            'entidad_type' => 'required|string|in:App\Models\Pelicula,App\Models\Director,App\Models\Actor',
            'entidad_id' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $modelClass = $request->input('entidad_type');
                    if (!class_exists($modelClass) || !$modelClass::where('id', $value)->exists()) {
                        $fail('El ID de la entidad seleccionada no es válido.');
                    }
                },
            ],
            'pelicula_id' => 'nullable|exists:peliculas,id'
        ], $this->mensajes());

        try {
            $nombre = $request->input('nombre');
            $categoria = $request->input('categoria');
            $anio = $request->input('anio');
            $entidadType = $request->input('entidad_type');
            $entidadId = $request->input('entidad_id');
            $peliculaId = $request->input('pelicula_id');
            $imagen = $this->getImagenPorNombre($nombre);


            if ($entidadType === 'App\Models\Pelicula') {
                $pelicula = Pelicula::findOrFail($entidadId);
                $anioEstreno = Carbon::parse($pelicula->estreno)->year;
                $anioSiguienteEstreno = Carbon::parse($pelicula->estreno)->addYear()->year;

                if ($anio < $anioEstreno) {
                    return redirect()->back()->withErrors([
                        'anio' => "El año del premio ($anio) debe ser igual o posterior al año de estreno de la película ($anioEstreno)."
                    ])->withInput();
                } elseif ($anio > $anioSiguienteEstreno) {
                    return redirect()->back()->withErrors([
                        'anio' => "El año del premio ($anio) debe coincidir con el año de estreno ($anioEstreno) o ser el año siguiente($anioSiguienteEstreno)."
                    ])->withInput();
                }
            } elseif ($entidadType === 'App\Models\Director') {
                $director = Director::findOrFail($entidadId);
                $anioNacDirector = Carbon::parse($director->fecha_nac)->year;
                $anioInicioActividadDirector = (int) $director->inicio_actividad;

                if ($peliculaId) {

                    $pelicula = Pelicula::findOrFail($peliculaId);
                    $anioEstreno = Carbon::parse($pelicula->estreno)->year;
                    $anioSiguienteEstreno = Carbon::parse($pelicula->estreno)->addYear()->year;

                    if ($anio < $anioEstreno) {
                        return redirect()->back()->withErrors([
                            'anio' => "El año del premio ($anio) debe ser igual o posterior al año de estreno de la película ($anioEstreno)."
                        ])->withInput();
                    } elseif ($anio > $anioSiguienteEstreno) {
                        return redirect()->back()->withErrors([
                            'anio' => "El año del premio ($anio) debe coincidir con el año de estreno ($anioEstreno) o ser el año siguiente ($anioSiguienteEstreno)."
                        ])->withInput();
                    }
                }

                if ($anio < $anioNacDirector) {
                    return redirect()->back()->withErrors([
                        'anio' => "El año del premio ($anio) no puede ser anterior al año de nacimiento del director ($anioNacDirector)."
                    ])->withInput();
                } elseif ($anio < $anioInicioActividadDirector) {
                    return redirect()->back()->withErrors([
                        'anio' => "El año del premio ($anio) no puede ser anterior al año de inicio de actividad del director ($anioInicioActividadDirector)."
                    ])->withInput();
                }
            } elseif ($entidadType === 'App\Models\Actor') {
                $actor = Actor::findOrFail($entidadId);
                $anioNacActor = Carbon::parse($actor->fecha_nac)->year;
                $anioInicioActividadActor = (int) $actor->inicio_actividad;

                if ($peliculaId) {

                    $pelicula = Pelicula::findOrFail($peliculaId);
                    $anioEstreno = Carbon::parse($pelicula->estreno)->year;
                    $anioSiguienteEstreno = Carbon::parse($pelicula->estreno)->addYear()->year;

                    if ($anio < $anioEstreno) {
                        return redirect()->back()->withErrors([
                            'anio' => "El año del premio ($anio) debe ser igual o posterior al año de estreno de la película ($anioEstreno)."
                        ])->withInput();
                    } elseif ($anio > $anioSiguienteEstreno) {
                        return redirect()->back()->withErrors([
                            'anio' => "El año del premio ($anio) debe coincidir con el año de estreno ($anioEstreno) o ser el año siguiente ($anioSiguienteEstreno)."
                        ])->withInput();
                    }
                }

                if ($anio < $anioNacActor) {
                    return redirect()->back()->withErrors([
                        'anio' => "El año del premio ($anio) no puede ser anterior al año de nacimiento del actor ($anioNacActor)."
                    ])->withInput();
                } elseif ($anio < $anioInicioActividadActor) {
                    return redirect()->back()->withErrors([
                        'anio' => "El año del premio ($anio) no puede ser anterior al año de inicio de actividad del actor ($anioInicioActividadActor)."
                    ])->withInput();
                }
            }


            if ($peliculaId) {
                $premioExistente = Premio::where('nombre', $nombre)
                    ->where('categoria', $categoria)
                    ->whereIn('anio', [$anioEstreno, $anioSiguienteEstreno])
                    ->where('pelicula_id', $peliculaId)
                    ->exists();

                if ($premioExistente) {
                    return redirect()->back()->withErrors([
                        'error' => "Ya existe un premio '$nombre' en la categoría '$categoria' para la película seleccionada en el año de estreno o el siguiente."
                    ])->withInput();
                }
            }

            switch ($request->input('entidad_type')) {
                case 'App\Models\Pelicula':
                    $entidad = Pelicula::findOrFail($request->input('entidad_id'));
                    break;
                case 'App\Models\Director':
                    $entidad = Director::findOrFail($request->input('entidad_id'));
                    break;
                case 'App\Models\Actor':
                    $entidad = Actor::findOrFail($request->input('entidad_id'));
                    break;
                default:
                    return redirect()->back()->withErrors(['error' => 'Entidad no validad']);
            }

            $premio = $entidad->premios()->create(array_merge(
                $request->only(['nombre', 'categoria', 'anio', 'pelicula_id']),
                ['imagen' => $imagen]
            ));

            return redirect()->route('premios.show', $premio->id)->with('success', 'Premio creado correctamente');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Error al crear el premio.' . $e->getMessage()
            ])->withInput();
        }

    }

    /**
     * @OA\Get(
     *     path="/premios/{id}/edit",
     *     summary="Mostrar formulario para editar un premio",
     *     description="Devuelve el formulario necesario para editar un premio existente. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="editPremio",
     *     tags={"Premios"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del premio",
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
     *             @OA\Property(property="premio", ref="#/components/schemas/Premio"),
     *             @OA\Property(property="peliculas", type="array", description="Lista de películas disponibles",
     *                 @OA\Items(ref="#/components/schemas/Pelicula")
     *             ),
     *             @OA\Property(property="directores", type="array", description="Lista de directores disponibles",
     *                 @OA\Items(ref="#/components/schemas/Director")
     *             ),
     *             @OA\Property(property="actores", type="array", description="Lista de actores disponibles",
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
     *         description="Premio no encontrado"
     *     )
     * )
     */

    public function edit($id)
    {
        $premio = Premio::findOrFail($id);
        $peliculas = Pelicula::all();
        $directores = Director::all();
        $actores = Actor::all();



        return view('premios.edit', compact('premio', 'peliculas', 'directores', 'actores'));
    }

    /**
     * @OA\Patch(
     *     path="/premios/{id}",
     *     summary="Actualizar un premio existente",
     *     description="Actualiza los datos de un premio existente y los asocia a una película, director o actor. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="updatePremio",
     *     tags={"Premios"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del premio",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"nombre", "categoria", "anio", "entidad_type", "entidad_id"},
     *             @OA\Property(property="nombre", type="string", description="Nombre del premio", example="Óscar"),
     *             @OA\Property(property="categoria", type="string", description="Categoría del premio", example="Mejor Película"),
     *             @OA\Property(property="anio", type="integer", description="Año del premio", example=2023),
     *             @OA\Property(property="entidad_type", type="string", description="Tipo de entidad asociada al premio", example="App\Models\Pelicula"),
     *             @OA\Property(property="entidad_id", type="integer", description="ID de la entidad asociada al premio", example=1),
     *             @OA\Property(property="pelicula_id", type="integer", description="ID de la película asociada al premio (opcional)", nullable=true, example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Premio actualizado con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Premio")
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
     *         description="Premio no encontrado"
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $rules = [
            'nombre' => 'required|string',
            'categoria' => 'required|string',
            'anio' => 'required|integer|min:1900|max:' . now()->year,
            'entidad_type' => 'required|string|in:App\Models\Pelicula,App\Models\Director,App\Models\Actor',
            'entidad_id' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $modelClass = $request->input('entidad_type');
                    if (!class_exists($modelClass) || !$modelClass::where('id', $value)->exists()) {
                        $fail('El ID de la entidad seleccionada no es válido.');
                    }
                },
            ],
            'pelicula_id' => 'nullable|exists:peliculas,id'
        ];

        $request->validate($rules, $this->mensajes());
        try {

            $premio = Premio::findOrFail($id);

            $nombre = $request->input('nombre');
            $categoria = $request->input('categoria');
            $anio = $request->input('anio');
            $entidadType = $request->input('entidad_type');
            $entidadId = $request->input('entidad_id');
            $peliculaId = $request->input('pelicula_id');
            $imagen = $this->getImagenPorNombre($nombre);

            if ($entidadType == 'App\Models\Pelicula') {
                $pelicula = Pelicula::findOrFail($entidadId);
                $anioEstreno = Carbon::parse($pelicula->estreno)->year;
                $anioSiguienteEstreno = Carbon::parse($pelicula->estreno)->addYear()->year;

                if ($anio < $anioEstreno) {
                    return redirect()->back()->withErrors([
                        'anio' => "El año del premio ($anio) debe ser igual o posterior al año de estreno de la película ($anioEstreno)."
                    ])->withInput();
                } elseif ($anio > $anioSiguienteEstreno) {
                    return redirect()->back()->withErrors([
                        'anio' => "El año del premio ($anio) debe coincidir con el año de estreno ($anioEstreno) o ser el año siguiente($anioSiguienteEstreno)."
                    ])->withInput();
                }
            } elseif ($entidadType == 'App\Models\Director') {
                $director = Director::findOrFail($entidadId);
                $anioNacDirector = Carbon::parse($director->fecha_nac)->year;
                $anioInicioActividadDirector = $director->inicio_actividad;

                if ($peliculaId) {

                    $pelicula = Pelicula::findOrFail($peliculaId);
                    $anioEstreno = Carbon::parse($pelicula->estreno)->year;
                    $anioSiguienteEstreno = Carbon::parse($pelicula->estreno)->addYear()->year;

                    if ($anio < $anioEstreno) {
                        return redirect()->back()->withErrors([
                            'anio' => "El año del premio ($anio) debe ser igual o posterior al año de estreno de la película ($anioEstreno)."
                        ])->withInput();
                    } elseif ($anio > $anioSiguienteEstreno) {
                        return redirect()->back()->withErrors([
                            'anio' => "El año del premio ($anio) debe coincidir con el año de estreno ($anioEstreno) o ser el año siguiente ($anioSiguienteEstreno)."
                        ])->withInput();
                    }
                }

                if ($anio < $anioNacDirector) {
                    return redirect()->back()->withErrors([
                        'anio' => "El año del premio ($anio) no puede ser anterior al año de nacimiento del director ($anioNacDirector)."
                    ])->withInput();
                } elseif ($anio < $anioInicioActividadDirector) {
                    return redirect()->back()->withErrors([
                        'anio' => "El año del premio ($anio) no puede ser anterior al año de inicio de actividad del director ($anioInicioActividadDirector)."
                    ])->withInput();
                }

            } elseif ($entidadType == 'App\Models\Actor') {
                $actor = Actor::findOrFail($entidadId);
                $anioNacActor = Carbon::parse($actor->fecha_nac)->year;
                $anioInicioActividadActor = $actor->inicio_actividad;

                if ($peliculaId) {

                    $pelicula = Pelicula::findOrFail($peliculaId);
                    $anioEstreno = Carbon::parse($pelicula->estreno)->year;
                    $anioSiguienteEstreno = Carbon::parse($pelicula->estreno)->addYear()->year;

                    if ($anio < $anioEstreno) {
                        return redirect()->back()->withErrors([
                            'anio' => "El año del premio ($anio) debe ser igual o posterior al año de estreno de la película ($anioEstreno)."
                        ])->withInput();
                    } elseif ($anio < $anioSiguienteEstreno) {
                        return redirect()->back()->withErrors([
                            'anio' => "El año del premio ($anio) debe coincidir con el año de estreno ($anioEstreno) o ser el año siguiente ($anioSiguienteEstreno)."
                        ])->withInput();
                    }
                }

                if ($anio < $anioNacActor) {
                    return redirect()->back()->withErrors([
                        'anio' => "El año del premio ($anio) no puede ser anterior al año de nacimiento del actor ($anioNacActor)."
                    ])->withInput();
                } elseif ($anio < $anioInicioActividadActor) {
                    return redirect()->back()->withErrors([
                        'anio' => "El año del premio ($anio) no puede ser anterior al año de inicio de actividad del actor ($anioInicioActividadActor)."
                    ])->withInput();
                }
            }

            $premioExistente = Premio::where('nombre', $nombre)
                ->where('categoria', $categoria)
                ->whereIn('anio', [$anioEstreno, $anioSiguienteEstreno])
                ->where('id', '!=', $id)
                ->exists();

            if ($premioExistente) {
                return redirect()->back()->withErrors([
                    'error' => 'Ya hay un premio con esas características asociado a una entidad.'
                ])->withInput();
            }

            switch ($entidadType) {
                case 'App\Models\Pelicula':
                    $entidad = Pelicula::findOrFail($entidadId);
                    break;
                case 'App\Models\Director':
                    $entidad = Director::findOrFail($entidadId);
                    break;
                case 'App\Models\Actor':
                    $entidad = Actor::findOrFail($entidadId);
                    break;
                default:
                    return redirect()->back()->withErrors(['error' => 'Entidad no valida']);
            }

            $premio->update([
                'nombre' => $nombre,
                'categoria' => $categoria,
                'anio' => $anio,
                'pelicula_id' => $peliculaId,
                'imagen' => $imagen,
            ]);

            $premio->entidad()->associate($entidad);

            if ($premio->imagen !== $imagen) {
                $premio->imagen = $imagen;
            }

            $premio->save();

            return redirect()->route('premios.show', $premio->id)->with('success', 'Premio editado correctamente');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el premio: ' . $e->getMessage()]);
        }

    }

    /**
     * @OA\Delete(
     *     path="/premios/{id}",
     *     summary="Eliminar un premio",
     *     description="Elimina un premio específico de forma lógica (soft delete). Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="deletePremio",
     *     tags={"Premios"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del premio",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Premio eliminado correctamente"
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
     *         description="Premio no encontrado"
     *     )
     * )
     */

    public function destroy($id)
    {
        try {
            $premio = Premio::findOrFail($id);
            $premio->delete();

            return redirect()->route('admin.premios')->with('success', 'Premio eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar el premio.']);
        }
    }

    /**
     * @OA\Get(
     *     path="/premios/eliminados",
     *     summary="Listar premios eliminados",
     *     description="Obtiene una lista paginada de premios que han sido eliminados lógicamente. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="getDeletedPremios",
     *     tags={"Premios"},
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
     *         description="Lista de premios eliminados obtenida con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", description="Página actual."),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Premio")),
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
        $premios = Premio::onlyTrashed()->paginate(4);

        return view('premios.deleted', compact('premios'));
    }

    /**
     * @OA\Patch(
     *     path="/premios/{id}/restaurar",
     *     summary="Restaurar un premio eliminado",
     *     description="Restaura un premio específico que fue eliminado lógicamente. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="restorePremio",
     *     tags={"Premios"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del premio",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Premio restaurado con éxito"
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
     *         description="Premio no encontrado"
     *     )
     * )
     */

    public function restore($id)
    {
        try {
            $premio = Premio::onlyTrashed()->findOrFail($id);
            $premio->restore();

            return redirect()->route('premios.deleted')->with('success', 'Premio restaurado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al restaurar el premio.']);
        }
    }


    public function mensajes()
    {
        return [
            'nombre.required' => 'El campo nombre es obligatorio',
            'nombre.string' => 'El campo nombre debe ser una cadena de caracteres',

            'categoria.required' => 'El campo categoría es obligatorio',
            'categoria.string' => 'El campo categoría debe ser una cadena de caracteres',

            'anio.required' => 'El campo año es obligatorio',
            'anio.integer' => 'El campo año debe ser un número entero',
            'anio.min' => 'El año mínimo permitido es :min',
            'anio.max' => 'El año máximo permitido es :max',

            'pelicula_id' => 'La película seleccionada no existe.',

            'entidad_type.in' => 'El tipo de entidad no es el correcto.'
        ];
    }

    /**
     * Obtiene la imagen asociada a un premio según su nombre.
     *
     * Este método toma un nombre de premio, lo convierte a minúsculas y busca
     * en un array predefinido de nombres de premios con sus respectivas rutas de imagen.
     * Si no encuentra una coincidencia, devuelve una imagen por defecto.
     *
     * @param string $nombre Nombre del premio.
     * @return string Ruta de la imagen asociada al premio o la imagen por defecto.
     *
     * Ejemplo:
     * - Si el nombre es "Oscar", retornará "premios/oscar.jpg".
     * - Si el nombre no está en el array (por ejemplo, "Emmy"), retornará la imagen por defecto.
     */

    public function getImagenPorNombre($nombre)
    {
        $imagenesPremios = [
            'oscar' => 'premios/oscar.jpg',
            'golden globe' => 'premios/golden_globe.jpg',
            'bafta' => 'premios/bafta.jpg',
            'cannes' => 'premios/cannes.jpg',
            'goya' => 'premios/goya.jpg',
            'saturn award' => 'premios/saturn_award.jpg',
            'directors guild of america' => 'premios/DGAAward.png'

        ];

        $nombreMin = strtolower($nombre);

        $imagen = $imagenesPremios[$nombreMin] ?? Premio::$IMAGEN_DEFAULT;

        return $imagen;
    }
}
