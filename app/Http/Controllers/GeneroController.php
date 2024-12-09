<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeneroController extends Controller
{
    /**
     * @OA\Get(
     *     path="/generos",
     *     summary="Obtener una lista de géneros",
     *     description="Devuelve una lista paginada de géneros ordenados alfabéticamente, con opción de búsqueda.",
     *     operationId="getGeneros",
     *     tags={"Generos"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Cadena de texto para buscar géneros por nombre.",
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
     *         description="Lista de géneros obtenida con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", description="Página actual."),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Genero")),
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
        $generos = Genero::search($request->search)->orderBy('nombre', 'asc')->paginate(8);

        return view('generos.index', compact('generos'));
    }

    /**
     * @OA\Get(
     *     path="/generos/{id}",
     *     summary="Obtener detalles de un género",
     *     description="Devuelve la información completa de un género específico junto con una lista paginada de películas asociadas.",
     *     operationId="getGenero",
     *     tags={"Generos"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del género",
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
     *         description="Detalles del género obtenidos con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", description="ID único del género"),
     *             @OA\Property(property="nombre", type="string", description="Nombre del género"),
     *             @OA\Property(property="peliculas", type="array", description="Lista de películas asociadas",
     *                 @OA\Items(ref="#/components/schemas/Pelicula")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Género no encontrado"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error en el servidor"
     *     )
     * )
     */

    public function show($id)
    {
        $genero = Genero::with('peliculas')->findOrFail($id);

        $peliculas = $genero->peliculas()->paginate(3);

        return view('generos.show', compact('genero', 'peliculas'));
    }

    /**
     * @OA\Get(
     *     path="/generos/create",
     *     summary="Mostrar formulario para crear un género",
     *     description="Devuelve el formulario necesario para crear un nuevo género. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="getGeneroCreate",
     *     tags={"Generos"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Formulario cargado con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="genero", ref="#/components/schemas/Genero")
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
        $genero = new Genero();

        return view('generos.create', compact('genero'));
    }

    /**
     * @OA\Post(
     *     path="/generos/store",
     *     summary="Crear un nuevo género",
     *     description="Crea un nuevo género y guarda los datos enviados, incluida una imagen opcional. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="createGenero",
     *     tags={"Generos"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"nombre"},
     *             @OA\Property(property="nombre", type="string", description="Nombre del género", example="Comedia"),
     *             @OA\Property(property="imagen", type="string", format="binary", description="Archivo de imagen opcional del género")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Género creado con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Genero")
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
            'nombre' =>'required|unique:generos|min:5|max:255',
        ], $this->mensajes());

        try{
            $genero = Genero::create($request->except('imagen'));

            if ($request->hasFile('imagen')){
                $imagen = $request->file('imagen');
                $extension = $imagen->getClientOriginalExtension();
                $fileToSave = $genero->id . '.' .$extension;
                $genero->imagen = $imagen->storeAs('generos', $fileToSave, 'public');
            }

            $genero->save();
            return redirect()->route('generos.show', $genero->id)->with('success', 'Género creado con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al crear el genero.']);
        }
    }

    /**
     * @OA\Get(
     *     path="/generos/{id}/edit",
     *     summary="Mostrar formulario para editar un género",
     *     description="Devuelve el formulario necesario para editar un género existente. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="editGenero",
     *     tags={"Generos"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del género",
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
     *             @OA\Property(property="genero", ref="#/components/schemas/Genero")
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
     *         description="Género no encontrado"
     *     )
     * )
     */

    public function edit($id)
    {
        $genero = Genero::findOrFail($id);

        return view('generos.edit', compact('genero'));
    }

    /**
     * @OA\Patch(
     *     path="/generos/{id}",
     *     summary="Actualizar un género existente",
     *     description="Actualiza los datos de un género existente, incluida una imagen opcional. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="updateGenero",
     *     tags={"Generos"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del género",
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
     *             @OA\Property(property="nombre", type="string", description="Nombre del género", example="Acción"),
     *             @OA\Property(property="imagen", type="string", format="binary", description="Archivo de imagen opcional del género")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Género actualizado con éxito",
     *         @OA\JsonContent(ref="#/components/schemas/Genero")
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
     *         description="Género no encontrado"
     *     )
     * )
     */

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' =>'required|min:5|max:255|unique:generos,nombre,'.$id,
        ], $this->mensajes());

        try{
            $genero = Genero::findOrFail($id);
            $genero->update($request->except('imagen'));

            if ($request->hasFile('imagen')){
                if ($genero->imagen != Genero::$IMAGEN_DEFAULT && Storage::exists($genero->imagen)){
                    Storage::delete($genero->imagen);
                }
                $imagen = $request->file('imagen');
                $extension = $imagen->getClientOriginalExtension();
                $fileToSave = $genero->id . '.' .$extension;
                $genero->imagen = $imagen->storeAs('generos', $fileToSave, 'public');
            }

            $genero->save();
            return redirect()->route('generos.show', $genero->id)->with('success', 'Género actualizado con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el genero.']);
        }
    }

    /**
     * @OA\Delete(
     *     path="/generos/{id}",
     *     summary="Eliminar un género",
     *     description="Elimina un género específico de forma lógica (soft delete). Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="deleteGenero",
     *     tags={"Generos"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del género",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Género eliminado con éxito"
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
     *         description="Género no encontrado"
     *     )
     * )
     */


    public function destroy($id)
    {
        try{
            $genero = Genero::findOrFail($id);

            $genero->delete();

            return redirect()->route('admin.generos')->with('success', 'Género eliminado con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar el genero.']);
        }
    }

    /**
     * @OA\Get(
     *     path="/generos/eliminados",
     *     summary="Listar géneros eliminados",
     *     description="Obtiene una lista paginada de géneros que han sido eliminados lógicamente. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="getDeletedGeneros",
     *     tags={"Generos"},
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
     *         description="Lista de géneros eliminados obtenida con éxito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", description="Página actual."),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Genero")),
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
        $generos = Genero::onlyTrashed()->paginate(4);

        return view('generos.deleted', compact('generos'));
    }

    /**
     * @OA\Patch(
     *     path="/generos/{id}/restaurar",
     *     summary="Restaurar un género eliminado",
     *     description="Restaura un género específico que fue eliminado lógicamente. Solo accesible para usuarios autenticados con rol ADMIN.",
     *     operationId="restoreGenero",
     *     tags={"Generos"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID único del género",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Género restaurado con éxito"
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
     *         description="Género no encontrado"
     *     )
     * )
     */

    public function restore($id)
    {
        try{
            $genero = Genero::onlyTrashed()->findOrFail($id);
            $genero->restore();

            return redirect()->route('generos.deleted')->with('success', 'Género restaurado con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al restaurar el genero.']);
        }
    }

    public function mensajes()
    {
        return [
            'nombre.required' => 'El campo nombre del genero es obligatorio.',
            'nombre.unique' => 'El nombre del genero ya existe.',
            'nombre.min' => 'El nombre del genero debe tener al menos 5 caracteres.',
            'nombre.max' => 'El nombre del genero no puede superar los 255 caracteres.'
        ];
    }
}
