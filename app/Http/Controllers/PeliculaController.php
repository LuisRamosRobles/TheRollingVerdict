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
        $peliculas = Pelicula::search($request->search)->orderBy('estreno', 'desc')->paginate(4);

        return view('peliculas.index', compact('peliculas'));
    }

    public function show($id, Request $request)
    {
        $pelicula = Pelicula::with(['generos', 'director', 'premios', 'actores'])->findOrFail($id);
        $referer = $request->input('referer', route('peliculas.index'));

        return view('peliculas.show', compact('pelicula', 'referer'));
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
        ];

    }
}
