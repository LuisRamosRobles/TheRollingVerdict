<?php

namespace App\Http\Controllers;

use App\Models\Director;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DirectorController extends Controller
{
    public function index(Request $request)
    {
        $directores = Director::search($request->search)->orderBy('nombre', 'asc')->paginate(4);

        return view('directores.index', compact('directores'));
    }

    public function show($id, Request $request)
    {
        $director = Director::with(['peliculas', 'premios'])->findOrFail($id);
        $referer = $request->input('referer', route('directores.index'));
        $peliculas = $director->peliculas()->paginate(5);

        return view('directores.show', compact('director', 'peliculas', 'referer'));
    }

    public function create()
    {
        $director = new Director();

        return view('directores.create', compact('director'));
    }

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

    public function edit($id)
    {
        $director = Director::findOrFail($id);

        return view('directores.edit', compact('director'));
    }

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

    public function deleted()
    {
        $directores = Director::onlyTrashed()->paginate(4);

        return view('directores.deleted', compact('directores'));
    }

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


    private function procesarImagen($director, $imagen)
    {

        $extension = $imagen->getClientOriginalExtension();
        $fileToSave = $director->id . '.' . $extension;

        return $imagen->storeAs('directores', $fileToSave, 'public');
    }




}
