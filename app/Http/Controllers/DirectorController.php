<?php

namespace App\Http\Controllers;

use App\Models\Director;
use App\Models\Pelicula;
use App\Models\Premio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DirectorController extends Controller
{
    public function index(Request $request) {
        $directores = Director::search($request->search)->orderBy('nombre', 'asc')->paginate(4);
        return view('directores.index', compact('directores'));
    }

    public function show($id) {
        $director = Director::with(['peliculas', 'premios'])->findOrFail($id);
        $peliculas = $director->peliculas()->paginate(5);


        return view('directores.show', compact('director', 'peliculas'));
    }

    public function create() {
        $director = new Director();
        $peliculas = Pelicula::orderBy('titulo', 'asc')->get();

        return view('directores.create', compact('director', 'peliculas'));
    }

    public function store(Request $request) {

        $request->validate([
            'nombre' => 'required|string|min:3|max:120',
            'fecha_nac' => 'nullable|date|date_format:Y-m-d|before_or_equal:today',
            'lugar_nac' => 'nullable|string|max:120',
            'biografia' => 'nullable|max:255',
            'inicio_actividad' => 'nullable|date|date_format:Y-m-d|before_or_equal:today',
            'activo' => 'boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'premios.*.nombre' => 'required|string',
            'premios.*.categoria' => 'required|string',
            'premios.*.anio' => 'required|integer|min:1900|max:' . now()->year,
        ], $this->mensajes());

        try {

            $nacimientoYear = $request->filled('fecha_nac')
                ? Carbon::parse($request->input('fecha_nac'))->year
                : null;

            $inicioActividadYear = $request->filled('inicio_actividad')
                ? Carbon::parse($request->input('inicio_actividad'))->year
                : null;

            $data = $request->except(['imagen', 'premios']);
            $data['fecha_nac'] = $request->filled('fecha_nac') ? $request->input('fecha_nac') : null;
            $data['inicio_actividad'] = $request->filled('inicio_actividad') ? $request->input('inicio_actividad') : null;

            if($request->has('premios')){
                foreach ($request->input('premios') as $index => $premio) {

                    if ($nacimientoYear && $premio['anio'] < $nacimientoYear) {
                        return redirect()->back()
                            ->withErrors([
                                "premios.{$index}.anio" => "El año del premio no puede ser anterior al año de nacimiento del director ({$nacimientoYear})."
                            ])
                            ->withInput();
                    }

                    if ($inicioActividadYear && $premio['anio'] < $inicioActividadYear) {
                        return redirect()->back()
                            ->withErrors([
                                "premios.{$index}.anio" => "El año del premio no puede ser  anterior al inicio de actividad del director ({$inicioActividadYear})."
                            ])
                            ->withInput();
                    }
                }
            }

            $director = Director::create($data);



            // Validar coherencia entre fechas
            if ($nacimientoYear && $inicioActividadYear && $inicioActividadYear < $nacimientoYear) {
                return redirect()->back()->withErrors([
                    'inicio_actividad' => 'La fecha de inicio de actividad no puede ser anterior a la fecha de nacimiento.'
                ])->withInput();
            }

            if ($request->has('premios')) {
                foreach ($request->input('premios', []) as $premioData) {
                    $nombre = $premioData['nombre'];
                    $imagen = $this->getImagenPorNombre($nombre);
                    $director->premios()->create(array_merge(
                        $premioData, ['imagen' => $imagen]));
                }
            }

            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $extension = $imagen->getClientOriginalExtension();
                $fileToSave = $director->id . '.' .$extension;
                $director->imagen = $imagen->storeAs('directores', $fileToSave, 'public');
            }

            $director->save();
            return redirect()->route('directores.show', $director->id)->with('success', 'Director creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al crear la película.']);
        }
    }

    public function edit($id) {
        $director = Director::with('premios')->findOrFail($id);
        $peliculas = Pelicula::orderBy('titulo', 'asc')->get();

        return view('directores.edit', compact('director', 'peliculas'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'nombre' => 'required|string|min:3|max:120',
            'fecha_nac' => 'nullable|date|date_format:Y-m-d|before_or_equal:today',
            'lugar_nac' => 'nullable|string|max:120',
            'biografia' => 'nullable|max:255',
            'inicio_actividad' => 'nullable|date|date_format:Y-m-d|before_or_equal:today',
            'activo' => 'boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'premios.*.nombre' => 'required|string|max:255',
            'premios.*.categoria' => 'required|string|max:255',
            'premios.*.anio' => 'required|integer|min:1900|max:' . now()->year,
            'premios.*.pelicula_id' => 'nullable|exists:peliculas,id',
        ], $this->mensajes());

        try {
            $director = Director::findOrFail($id);

            $director->update($request->except('imagen', 'premios', 'premios_eliminar'));

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
                    $nuevoPremio = $director->premios()->create($data);
                    $premiosIds[] = $nuevoPremio->id;
                }
            }

            if ($request->filled('premios_eliminar')) {
                $idsEliminar = explode(',', $request->input('premios_eliminar'));
                $director->premios()
                    ->whereIn('id', $idsEliminar)
                    ->delete(); // Soft delete
            }

            if ($request->filled('fecha_nac') && $request->filled('inicio_actividad')) {
                $fechaNac = Carbon::parse($request->input('fecha_nac'));
                $inicioActividad = Carbon::parse($request->input('inicio_actividad'));

                if ($inicioActividad < $fechaNac) {
                    return redirect()->back()->withErrors([
                        'inicio_actividad' => 'La fecha de inicio de actividad no puede ser anterior a la fecha de nacimiento.'
                    ])->withInput();
                }
            }

            if ($request->hasFile('imagen')) {

                if ($director->imagen != Director::$IMAGEN_DEFAULT && Storage::exists($director->imagen)) {
                    Storage::delete($director->imagen);
                }
                $imagen = $request->file('imagen');
                $extension = $imagen->getClientOriginalExtension();
                $fileToSave = $director->id . '.' .$extension;
                $director->imagen = $imagen->storeAs('directores', $fileToSave, 'public');

            }

            $director->save();
            return redirect()->route('directores.show', $director->id)->with('success', 'Director actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el director.']);
        }
    }

    public function destroy($id) {
        try {
            $director = Director::findOrFail($id);

            if ($director->imagen!= Director::$IMAGEN_DEFAULT && Storage::exists($director->imagen)) {
                Storage::delete($director->imagen);
            }
            $director->imagen = Director::$IMAGEN_DEFAULT;
            $director->save();

            $director->delete();
            return redirect()->route('directores.index')->with('success', 'Director eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar el director.']);
        }
    }

    public function deleted() {
        $directores = Director::onlyTrashed()->paginate(4);

        return view('directores.deleted', compact('directores'));
    }

    public function restore($id) {
        try {
            $director = Director::onlyTrashed()->findOrFail($id);
            $director->restore();

            return redirect()->route('directores.deleted')->with('success', 'Director restaurado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al restaurar el director.']);
        }
    }

    public function mensajes(){
        return [
            'nombre.required' => 'El campo nombre es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre debe tener máximo 120 caracteres',

            'fecha_nac.date' => 'El campo fecha de nacimiento debe ser una fecha válida',
            'fecha_nac.before_or_equal' => 'La fecha de nacimiento no puede ser posterior a la actual',

            'lugar_nac.max' => 'El lugar de nacimiento debe tener máximo 120 caracteres',

            'biografia.max' => 'La biografía debe tener máximo 255 caracteres',

            'inicio_actividad.date' => 'El campo fecha de inicio de actividad debe ser una fecha válida',
            'inicio_actividad.before_or_equal' => 'La fecha de inicio de actividad no puede ser posterior a la actual',

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
