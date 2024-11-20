<?php

namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\Pelicula;
use App\Models\Premio;
use Illuminate\Http\Request;

class PremioController extends Controller
{
    public function index(Request $request) {
        $premios = Premio::search($request->search)
            ->orderBy('anio', 'desc')
            ->orderBy('nombre', 'asc')
            ->paginate(4);

        return view('premios.index', compact('premios'));
    }

    public function show($id) {
        $premio = Premio::findOrFail($id);

        return view('premios.show', compact('premio'));
    }

    public function create() {
        $premio = new Premio();
        $peliculas = Pelicula::all();
        $directores = Director::all();

        return view('premios.create', compact('premio','peliculas', 'directores'));
    }

    public function store(Request $request) {
        $request->validate([
            'nombre' => 'required|string',
            'categoria' => 'required|string',
            'anio' => 'required|integer|min:1900|max:' . now()->year,
            'entidad_type' => 'required|string|in:App\Models\Pelicula,App\Models\Director',
            'entidad_id' => 'required|integer',
            'pelicula_id' => 'nullable|exists:peliculas,id'
        ], $this->mensajes());

        try {
            $nombre = $request->input('nombre');
            $imagen = $this->getImagenPorNombre($nombre);

            switch ($request->input('entidad_type')) {
                case 'App\Models\Pelicula':
                    $entidad = Pelicula::findOrFail($request->input('entidad_id'));
                    break;
                case 'App\Models\Director':
                    $entidad = Director::findOrFail($request->input('entidad_id'));
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
            return redirect()->back()->withErrors(['error' => 'Error al crear el premio.' . $e->getMessage()]);
        }

    }

    public function edit($id) {
        $premio = Premio::findOrFail($id);
        $peliculas = Pelicula::all();
        $directores = Director::all();

        return view('premios.edit', compact('premio', 'peliculas', 'directores'));
    }

    public function update(Request $request, $id) {
        $rules = [
            'nombre' => 'required|string',
            'categoria' => 'required|string',
            'anio' => 'required|integer|min:1900|max:' . now()->year,
            'entidad_type' => 'required|string|in:App\Models\Pelicula,App\Models\Director',
            'entidad_id' => 'required|integer',
            'pelicula_id' => 'nullable|exists:peliculas,id'
        ];
        try {
            if ($request->input('entidad_type') === 'App\Models\Director') {
                $rules['pelicula_id'] = 'required|exists:peliculas,id';
            }

            $request->validate($rules, $this->mensajes());

            $premio = Premio::findOrFail($id);
            $premio->update($request->only(['nombre','categoria','anio']));

            switch ($request->input('entidad_type')) {
                case 'App\Models\Pelicula':
                    $entidad = Pelicula::findOrFail($request->input('entidad_id'));
                    break;
                case 'App\Models\Director':
                    $entidad = Director::findOrFail($request->input('entidad_id'));
                    break;
                default:
                    return redirect()->back()->withErrors(['error' => 'Entidad no valida']);
            }

            $premio->entidad()->associate($entidad);

            $nombre = $request->input('nombre');
            $imagen = $this->getImagenPorNombre($nombre);
            if ($premio->imagen !== $imagen) {
                $premio->imagen = $imagen;
            }

            $premio->save();

            return redirect()->route('premios.show', $premio->id)->with('success', 'Premio editado correctamente');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el premio.']);
        }

    }

    public function destroy($id) {
        try {
            $premio = Premio::findOrFail($id);
            $premio->delete();


            return redirect()->route('premios.index')->with('success', 'Premio eliminado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar el premio.']);
        }
    }

    public function deleted() {
        $premios = Premio::onlyTrashed()->paginate(4);

        return view('premios.deleted', compact('premios'));
    }

    public function restore($id) {
        try {
            $premio = Premio::onlyTrashed()->findOrFail($id);
            $premio->restore();

            return redirect()->route('premios.deleted')->with('success', 'Premio restaurado correctamente');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al restaurar el premio.']);
        }
    }


    public function mensajes() {
        return [
            'nombre.required' => 'El campo nombre es obligatorio',
            'nombre.string' => 'El campo nombre debe ser una cadena de caracteres',

            'categoria.required' => 'El campo categoría es obligatorio',
            'categoria.string' => 'El campo categoría debe ser una cadena de caracteres',

            'anio.required' => 'El campo año es obligatorio',
            'anio.integer' => 'El campo año debe ser un número entero',
            'anio.min' => 'El año mínimo permitido es :min',
            'anio.max' => 'El año máximo permitido es :max',

            'pelicula_id' => 'La película seleccionada no existe.'
        ];
    }

    public function getImagenPorNombre($nombre) {
        $imagenesPremios = [
            'oscar' => 'premios/oscar.jpg',
            'golden globe' => 'premios/golden_globe.jpg',
            'bafta' => 'premios/bafta.jpg',
            'cannes' => 'premios/cannes.jpg',
            'goya' => 'premios/goya.jpg',
            'saturn award' => 'premios/saturn_award.jpg',
            'directors guild of america' => 'premios/DGAAward.png'
            // Agrega más asociaciones aquí
        ];

        $nombreMin = strtolower($nombre);
        // Buscar la imagen según el nombre del premio
        $imagen = $imagenesPremios[$nombreMin] ?? Premio::$IMAGEN_DEFAULT;

        return $imagen;
    }
}
