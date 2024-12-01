<?php

use App\Http\Controllers\DirectorController;
use App\Http\Controllers\ActorController;
use App\Http\Controllers\GeneroController;
use App\Http\Controllers\PremioController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeliculaController;

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', function () {
    return redirect()->route('peliculas.index');
});


Route::get('/test-redis', function () {
    // Intenta almacenar un valor en Redis
    Cache::put('prueba', 'Hola Redis', 10); // 10 minutos de duración

    // Recupera el valor almacenado
    $valor = Cache::get('prueba');

    return response()->json([
        'mensaje' => 'Prueba de Redis',
        'valor_guardado' => $valor
    ]);
});

Route::group(['prefix'=> 'peliculas'], function(){
    Route::get('/', [PeliculaController::class, 'index'])->name('peliculas.index');

    Route::get('/create', [PeliculaController::class, 'create'])->name('peliculas.create');

    Route::post('/store', [PeliculaController::class, 'store'])->name('peliculas.store');

    Route::get('/eliminados', [PeliculaController::class, 'deleted'])->name('peliculas.deleted');

    Route::post('/{id}/restaurar', [PeliculaController::class, 'restore'])->name('peliculas.restore');

    Route::get('/{id}', [PeliculaController::class,'show'])->name('peliculas.show');

    Route::get('/{id}/edit', [PeliculaController::class, 'edit'])->name('peliculas.edit');

    Route::patch('/{id}', [PeliculaController::class, 'update'])->name('peliculas.update');

    Route::delete('/{id}', [PeliculaController::class, 'destroy'])->name('peliculas.destroy');
});

Route::group(['prefix'=> 'generos'], function(){
    Route::get('/', [GeneroController::class, 'index'])->name('generos.index');

    Route::get('/create', [GeneroController::class, 'create'])->name('generos.create');

    Route::post('/store', [GeneroController::class,'store'])->name('generos.store');

    Route::get('/eliminados', [GeneroController::class, 'deleted'])->name('generos.deleted');

    Route::post('/{id}/restaurar', [GeneroController::class, 'restore'])->name('generos.restore');

    Route::get('/{id}', [GeneroController::class,'show'])->name('generos.show');

    Route::get('/{id}/edit', [GeneroController::class, 'edit'])->name('generos.edit');

    Route::patch('/{id}', [GeneroController::class, 'update'])->name('generos.update');

    Route::delete('/{id}', [GeneroController::class, 'destroy'])->name('generos.destroy');
});

Route::group(['prefix' => 'directores'], function (){
    Route::get('/', [DirectorController::class, 'index'])->name('directores.index');

    Route::get('/create', [DirectorController::class, 'create'])->name('directores.create');

    Route::post('/store', [DirectorController::class,'store'])->name('directores.store');

    Route::get('/eliminados', [DirectorController::class, 'deleted'])->name('directores.deleted');

    Route::post('/{id}/restaurar', [DirectorController::class,'restore'])->name('directores.restore');

    Route::get('/{id}', [DirectorController::class,'show'])->name('directores.show');

    Route::get('/{id}/edit', [DirectorController::class, 'edit'])->name('directores.edit');

    Route::patch('/{id}', [DirectorController::class, 'update'])->name('directores.update');

    Route::delete('/{id}', [DirectorController::class, 'destroy'])->name('directores.destroy');
});

Route::group(['prefix' => 'actores'], function (){
    Route::get('/', [ActorController::class, 'index'])->name('actores.index');

    Route::get('/create', [ActorController::class, 'create'])->name('actores.create');

    Route::post('/store', [ActorController::class,'store'])->name('actores.store');

    Route::get('/eliminados', [ActorController::class, 'deleted'])->name('actores.deleted');

    Route::post('/{id}/restaurar', [ActorController::class,'restore'])->name('actores.restore');

    Route::get('/{id}', [ActorController::class,'show'])->name('actores.show');

    Route::get('/{id}/edit', [ActorController::class, 'edit'])->name('actores.edit');

    Route::patch('/{id}', [ActorController::class, 'update'])->name('actores.update');

    Route::delete('/{id}', [ActorController::class, 'destroy'])->name('actores.destroy');
});

Route::group(['prefix' => 'premios'], function (){
    Route::get('/', [PremioController::class, 'index'])->name('premios.index');

    Route::get('/create', [PremioController::class, 'create'])->name('premios.create');

    Route::post('/store', [PremioController::class,'store'])->name('premios.store');

    Route::get('/eliminados', [PremioController::class, 'deleted'])->name('premios.deleted');

    Route::post('/{id}/restaurar', [PremioController::class,'restore'])->name('premios.restore');

    Route::get('/{id}', [PremioController::class,'show'])->name('premios.show');

    Route::get('/{id}/edit', [PremioController::class, 'edit'])->name('premios.edit');

    Route::patch('/{id}', [PremioController::class, 'update'])->name('premios.update');

    Route::delete('/{id}', [PremioController::class, 'destroy'])->name('premios.destroy');
});
