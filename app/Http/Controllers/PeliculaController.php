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
    public function index(Request $request)
    {
        $peliculas = Pelicula::search($request->search)->orderBy('estreno', 'desc')->paginate(3);

        return view('peliculas.index', compact('peliculas'));
    }

    public function show($id)
    {
        $pelicula = Pelicula::with(['generos', 'director', 'premios', 'actores'])->findOrFail($id);

        return view('peliculas.show', compact('pelicula'));
    }

    public function create()
    {
        $pelicula = new Pelicula();
        $generos = Genero::orderBy('nombre', 'asc')->get();
        $directores = Director::where('activo', true)->orderBy('nombre', 'asc')->get();
        $actores = Actor::where('activo', true)->orderBy('nombre', 'asc')->get();

        return view('peliculas.create', compact('pelicula', 'generos', 'directores', 'actores'));

    }

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
            'premios.*.nombre' => 'required|string',
            'premios.*.categoria' => 'required|string',
            'premios.*.anio' => 'required|integer|min:1900|max:' . now()->year,
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

            if ($request->has('premios')) {
                foreach ($request->input('premios') as $index => $premio) {
                    if ($anioEstreno && $premio['anio'] < $anioEstreno) {
                        return redirect()->back()
                            ->withErrors([
                                "premios.{$index}.anio" => "El año del premio no puede ser anterior al año de estreno de la película ({$anioEstreno})."
                            ])->withInput();
                    } elseif ($anioSiguienteEstreno && $premio['anio'] > $anioSiguienteEstreno) {
                        return redirect()->back()
                            ->withErrors([
                                "premios.{$index}.anio" => "El año del premio debe coincidir con el año de estreno ($anioEstreno) o ser el año siguiente ($anioSiguienteEstreno)."
                            ])->withInput();
                    }

                    if ($this->validarPremiosDuplicados($premio)) {
                        return redirect()->back()->withErrors([
                            'error' => 'El premio que intentas asociar con esas características ya existe en la base de datos.'
                        ])->withInput();
                    }

                }
            }


            $pelicula = Pelicula::create($request->except(['imagen', 'generos', 'reparto','premios']));

            if ($request->hasFile('imagen')){
                $imagen = $request->file('imagen');
                $extension = $imagen->getClientOriginalExtension();
                $fileToSave = $pelicula->id . '.' .$extension;
                $pelicula->imagen = $imagen->storeAs('peliculas', $fileToSave, 'public');
            }

            if ($request->has('premios')){
                foreach ($request->input('premios', []) as $premioData) {
                    $nombre = $premioData['nombre'];
                    $imagen = $this->getImagenPorNombre($nombre);
                    $pelicula->premios()->create(array_merge(
                        $premioData, ['imagen' => $imagen]));
                }
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

    public function edit($id)
    {
        $pelicula = Pelicula::with(['generos', 'director', 'actores'])->findOrFail($id);
        $generos = Genero::orderBy('nombre', 'asc')->get();
        $directores = Director::where('activo', true)->get();

        //dd($pelicula->actores);

        // Actores que están actualmente seleccionados en el reparto
        $repartoSeleccionado = $pelicula->actores;

        // Actores activos que no están en el reparto
        $actoresDisponibles = Actor::where('activo', true)
            ->whereNotIn('id', $repartoSeleccionado->pluck('id'))
            ->get();

        //dd($repartoSeleccionado, $actoresDisponibles);

        return view('peliculas.edit', compact(
            'pelicula',
            'generos',
            'directores',
            'repartoSeleccionado',
            'actoresDisponibles'));
    }

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
            'premios.*.nombre' => 'required|string|max:255',
            'premios.*.categoria' => 'required|string|max:255',
            'premios.*.anio' => 'required|integer|min:1900|max:' . now()->year,
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

            if ($request->has('premios')) {
                foreach ($request->input('premios') as $index => $premio) {
                    if ($anioEstreno && $premio['anio'] < $anioEstreno) {
                        return redirect()->back()
                            ->withErrors([
                                "premios.{$index}.anio" => "El año del premio no puede ser anterior al año de estreno de la película ({$anioEstreno})."
                            ])
                            ->withInput();
                    } elseif ($anioSiguienteEstreno && $premio['anio'] > $anioSiguienteEstreno) {
                        return redirect()->back()
                            ->withErrors([
                                "premios.{$index}.anio" => "El año del premio debe coincidir con el año de estreno ($anioEstreno) o ser el año siguiente ($anioSiguienteEstreno)."
                            ])->withInput();
                    }

                    if ($this->validarPremiosDuplicados($premio)) {
                        return redirect()->back()->withErrors([
                            'error' => 'El premio que intentas asociar con esas características ya existe en la base de datos.'
                        ])->withInput();
                    }

                }
            }



            $pelicula->update($request->except('imagen', 'generos', 'premios', 'reparto'));

            if ($request->has('reparto')) {
                //dd($request->input('reparto'));
                $pelicula->actores()->sync($request->input('reparto'));
            }

            $premiosIds = [];
            foreach ($request->input('premios', []) as $data) {
                if (isset($data['id'])) {
                    $premio = Premio::findOrFail($data['id']);
                    if ($premio->nombre !== $data['nombre']) {
                        $data['imagen'] = $this->getImagenPorNombre($data['nombre']);
                    }
                    $premio->update($data);
                    $premiosIds[] = $premio->id;
                } else {
                    $nuevoPremio = $pelicula->premios()->create($data);
                    $premiosIds[] = $nuevoPremio->id;
                }
            }

            if ($request->filled('premios_eliminar')) {
                $idsEliminar = explode(',', $request->input('premios_eliminar'));
                $pelicula->premios()
                    ->whereIn('id', $idsEliminar)
                    ->delete(); // Soft delete
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

    public function destroy($id)
    {
        try {
            $pelicula = Pelicula::findOrFail($id);
            if ($pelicula->imagen && $pelicula->imagen != Pelicula::$IMAGEN_DEFAULT) {
                Storage::disk('public')->delete($pelicula->imagen);
            }
            $pelicula->imagen = Pelicula::$IMAGEN_DEFAULT;
            $pelicula->save();

            $pelicula->delete();

            return redirect()->route('peliculas.index')->with('success', 'Película eliminada con éxito.');
        }catch (\Exception $e){
            return redirect()->back()->withErrors(['error' => 'Error al eliminar la película.']);
        }
    }

    public function deleted()
    {
        $peliculas = Pelicula::onlyTrashed()->paginate(4);

        return view('peliculas.deleted', compact('peliculas'));
    }

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

            'premios.*.nombre.required' => 'El campo nombre del premio es obligatorio.',
            'premios.*.nombre.string' => 'El campo nombre del premio debe ser una cadena.',
            'premios.*.nombre.max' => 'El campo nombre del premio no puede superar los 255 caracteres.',

            'premios.*.categoria.required' => 'El campo categoría del premio es obligatorio.',
            'premios.*.categoria.string' => 'El campo categoría del premio debe ser una cadena.',
            'premios.*.categoria.max' => 'El campo categoría del premio no puede superar los 255 caracteres.',

            'premios.*.anio.required' => 'El campo año del premio es obligatorio.',
            'premios.*.anio.integer' => 'El campo año del premio debe ser un número entero.',
            'premios.*.anio.min' => 'El campo año del premio debe ser al menos 1900.',
            'premios.*.anio.max' => 'El campo año del premio no puede superar '. now()->year,
        ];

    }

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
            // Agrega más asociaciones aquí
        ];

        $nombreMin = strtolower($nombre);
        // Buscar la imagen según el nombre del premio
        $imagen = $imagenesPremios[$nombreMin] ?? Premio::$IMAGEN_DEFAULT;

        return $imagen;
    }

    private function validarPremiosDuplicados($premio)
    {
        return Premio::where('nombre', $premio['nombre'])
            ->where('categoria', $premio['categoria'])
            ->where('anio', $premio['anio'])
            ->where('id', '!=', $premio['id'] ?? null)
            ->exists();
    }
}
