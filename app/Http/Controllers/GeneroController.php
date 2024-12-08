<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GeneroController extends Controller
{
    public function index(Request $request)
    {
        $generos = Genero::search($request->search)->orderBy('nombre', 'asc')->paginate(8);

        return view('generos.index', compact('generos'));
    }

    public function show($id)
    {
        $genero = Genero::with('peliculas')->findOrFail($id);

        $peliculas = $genero->peliculas()->paginate(3);

        return view('generos.show', compact('genero', 'peliculas'));
    }

    public function create()
    {
        $genero = new Genero();

        return view('generos.create', compact('genero'));
    }

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

    public function edit($id)
    {
        $genero = Genero::findOrFail($id);

        return view('generos.edit', compact('genero'));
    }

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

    public function deleted()
    {
        $generos = Genero::onlyTrashed()->paginate(4);

        return view('generos.deleted', compact('generos'));
    }

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
