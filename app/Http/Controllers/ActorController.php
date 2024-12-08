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
    public function index(Request $request)
    {
        $actores = Actor::search($request->search)->orderBy('nombre', 'asc')->paginate(4);

        return view('actores.index', compact('actores'));
    }

    public function show($id, Request $request)
    {
        $actor = Actor::with(['peliculas', 'premios'])->findOrFail($id);
        $referer = $request->input('referer', route('actores.index'));
        $peliculas = $actor->peliculas()->paginate(5);

        return view('actores.show', compact('actor', 'peliculas', 'referer'));
    }

    public function create()
    {
        $actor = new Actor();

        return view('actores.create', compact('actor'));
    }

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

    public function edit($id)
    {
        $actor = Actor::findOrFail($id);
        $peliculas = Pelicula::orderBy('titulo', 'asc')->get();

        return view('actores/edit', compact('actor', 'peliculas'));
    }

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

    public function deleted()
    {
        $actores = Actor::onlyTrashed()->paginate(4);

        return view('actores.deleted', compact('actores'));
    }

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



    private function procesarImagen($actor, $imagen)
    {

        $extension = $imagen->getClientOriginalExtension();
        $fileToSave = $actor->id . '.' . $extension;

        return $imagen->storeAs('actores', $fileToSave, 'public');
    }
}
