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

    public function show($id)
    {
        $actor = Actor::with(['peliculas', 'premios'])->findOrFail($id);
        $peliculas = $actor->peliculas()->paginate(5);

        return view('actores.show', compact('actor', 'peliculas'));
    }

    public function create()
    {
        $actor = new Actor();

        return view('actores.create', compact('actor'));
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

            $data = $request->except(['imagen', 'premios']);
            $data['fecha_nac'] = $request->filled('fecha_nac') ? $request->input('fecha_nac') : null;
            $data['inicio_actividad'] = $request->filled('inicio_actividad') ? $request->input('inicio_actividad') : null;
            $data['fin_actividad'] = $request->filled('fin_actividad')? $request->input('fin_actividad') : null;

            /*if($request->has('premios')){
                foreach ($request->input('premios') as $index => $premio) {

                    if ($nacimientoYear && $premio['anio'] < $nacimientoYear) {
                        return redirect()->back()
                            ->withErrors([
                                "premios.{$index}.anio" => "El año del premio no puede ser anterior al año de nacimiento del actor ({$nacimientoYear})."
                            ])->withInput();
                    }

                    if ($inicioActividadYear && $premio['anio'] < $inicioActividadYear) {
                        return redirect()->back()
                            ->withErrors([
                                "premios.{$index}.anio" => "El año del premio no puede ser  anterior al inicio de actividad del actor ({$inicioActividadYear})."
                            ])->withInput();
                    }

                    if (!empty($premio['pelicula_id'])) {
                        $pelicula = Pelicula::findOrFail($premio['pelicula_id']);
                        if ($pelicula) {
                            $anioEstreno = Carbon::parse($pelicula->estreno)->year;
                            $anioSiguienteEstreno = Carbon::parse($pelicula->estreno)->addYear()->year;

                            if ($premio['anio'] < $anioEstreno) {
                                return redirect()->back()->withErrors([
                                    'anio' => "El año del premio debe ser igual o posterior al año de estreno de la película ($anioEstreno)."
                                ])->withInput();
                            } elseif ($premio['anio'] > $anioSiguienteEstreno) {
                                return redirect()->back()->withErrors([
                                    'anio' => "El año del premio debe coincidir con el año de estreno ($anioEstreno) o ser el año siguiente ($anioSiguienteEstreno)."
                                ])->withInput();
                            }
                        }
                    }

                    if ($this->validarPremiosDuplicados($premio)) {
                        return redirect()->back()->withErrors([
                            'error' => 'El premio que intentas asociar con esas características ya existe en la base de datos.'
                        ])->withInput();
                    }
                }
            }*/

            $actor = Actor::create($data);

            /*if ($request->has('premios')) {
                foreach ($request->input('premios', []) as $premioData) {
                    $nombre = $premioData['nombre'];
                    $imagen = $this->getImagenPorNombre($nombre);
                    $actor->premios()->create(array_merge(
                        $premioData, ['imagen' => $imagen]));
                }
            }*/

            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $extension = $imagen->getClientOriginalExtension();
                $fileToSave = $actor->id . '.' .$extension;
                $actor->imagen = $imagen->storeAs('actores', $fileToSave, 'public');
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

            foreach ($request->input('premios', []) as $index => $premio) {
                if ($nacimientoYear && $premio['anio'] < $nacimientoYear) {
                    return redirect()->back()->withErrors([
                        "premios.{$index}.anio" => "El año del premio no puede ser anterior al año de nacimiento del actor ({$nacimientoYear})."
                    ])->withInput();
                }

                if ($inicioActividadYear && $premio['anio'] < $inicioActividadYear) {
                    return redirect()->back()->withErrors([
                        "premios.{$index}.anio" => "El año del premio no puede ser anterior al inicio de actividad del actor ({$inicioActividadYear})."
                    ])->withInput();
                }

                if (!empty($premio['pelicula_id'])) {
                    $pelicula = Pelicula::findOrFail($premio['pelicula_id']);
                    if ($pelicula) {
                        $anioEstreno = Carbon::parse($pelicula->estreno)->year;
                        $anioSiguienteEstreno = Carbon::parse($pelicula->estreno)->addYear()->year;

                        if ($premio['anio'] < $anioEstreno) {
                            return redirect()->back()->withErrors([
                                'anio' => "El año del premio debe ser igual o posterior al año de estreno de la película ($anioEstreno)."
                            ])->withInput();
                        } elseif ($premio['anio'] > $anioSiguienteEstreno) {
                            return redirect()->back()->withErrors([
                                'anio' => "El año del premio debe coincidir con el año de estreno ($anioEstreno) o ser el año siguiente ($anioSiguienteEstreno)."
                            ])->withInput();
                        }
                    }
                }

                if ($this->validarPremiosDuplicados($premio)) {
                    return redirect()->back()->withErrors([
                        'error' => 'El premio que intentas asociar con esas características ya existe en la base de datos.'
                    ])->withInput();
                }
            }

            $actor->update($request->except('imagen', 'premios', 'premios_eliminar'));

            $this->procesarPremios($actor, $request->input('premios', []), $request->input('premios_eliminar', ''));

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
            if ($actor->imagen && $actor->imagen!= Actor::$IMAGEN_DEFAULT) {
                Storage::delete($actor->imagen);
            }
            $actor->delete();
            return redirect()->route('actores.index')->with('success', 'Actor eliminado correctamente.');
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

            'premios.*.nombre.required' => 'El campo nombre del premio es obligatorio',
            'premios.*.nombre.string' => 'El campo nombre del premio debe ser una cadena de caracteres',

            'premios.*.categoria.required' => 'El campo categoría del premio es obligatorio',
            'premios.*.categoria.string' => 'El campo categoría del premio debe ser una cadena de caracteres',

            'premios.*.anio.required' => 'El campo año del premio es obligatorio',
            'premios.*.anio.integer' => 'El campo año del premio debe ser un número entero',
            'premios.*.anio.min' => 'El año del premio debe ser un número mayor o igual a 1900',
            'premios.*.anio.max' => 'El año del premio debe ser un número menor o igual a '. now()->year,
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

    private function procesarImagen($actor, $imagen)
    {
        if ($actor->imagen != Actor::$IMAGEN_DEFAULT && Storage::exists($actor->imagen)) {
            Storage::delete($actor->imagen);
        }

        $extension = $imagen->getClientOriginalExtension();
        $fileToSave = $actor->id . '.' . $extension;

        return $imagen->storeAs('actores', $fileToSave, 'public');
    }



    private function validarPremiosDuplicados($premio)
    {
        return Premio::where('nombre', $premio['nombre'])
            ->where('categoria', $premio['categoria'])
            ->where('anio', $premio['anio'])
            ->where('id', '!=', $premio['id'] ?? null)
            ->exists();
    }

    private function procesarPremios($actor, array $premios, $premiosEliminar)
    {
        $premiosIds = [];
        foreach ($premios as $data) {
            if (isset($data['id'])) {
                $premio = Premio::findOrFail($data['id']);
                if ($premio->nombre !== $data['nombre']) {
                    $data['imagen'] = $this->getImagenPorNombre($data['nombre']);
                }
                $premio->update($data);
                $premiosIds[] = $premio->id;
            } else {
                $data['imagen'] = $this->getImagenPorNombre($data['nombre']);
                $nuevoPremio = $actor->premios()->create($data);
                $premiosIds[] = $nuevoPremio->id;
            }
        }

        if ($premiosEliminar) {
            $idsEliminar = explode(',', $premiosEliminar);
            $actor->premios()->whereIn('id', $idsEliminar)->delete();
        }
    }


}
