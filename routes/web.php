<?php

use App\Http\Controllers\ActorController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DirectorController;
use App\Http\Controllers\GeneroController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\PremioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResenaController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;


Route::get('/', [IndexController::class, 'index'])->name('index');


Route::group(['prefix'=> 'peliculas'], function(){
    Route::get('/', [PeliculaController::class, 'index'])->name('peliculas.index');

    Route::get('/create', [PeliculaController::class, 'create'])->name('peliculas.create')->middleware(['auth', 'admin']);

    Route::post('/store', [PeliculaController::class, 'store'])->name('peliculas.store')->middleware(['auth', 'admin']);

    Route::get('/eliminados', [PeliculaController::class, 'deleted'])->name('peliculas.deleted')->middleware(['auth', 'admin']);

    Route::post('/{id}/restaurar', [PeliculaController::class, 'restore'])->name('peliculas.restore')->middleware(['auth', 'admin']);

    Route::get('/{id}', [PeliculaController::class,'show'])->name('peliculas.show');

    Route::get('/{id}/edit', [PeliculaController::class, 'edit'])->name('peliculas.edit')->middleware(['auth', 'admin']);

    Route::patch('/{id}', [PeliculaController::class, 'update'])->name('peliculas.update')->middleware(['auth', 'admin']);

    Route::delete('/{id}', [PeliculaController::class, 'destroy'])->name('peliculas.destroy')->middleware(['auth', 'admin']);


    Route::post('/{id}/resenas', [ResenaController::class, 'store'])->name('resenas.store')->middleware('auth');
    Route::delete('/resenas/{resena}', [ResenaController::class, 'destroy'])->name('resenas.destroy')->middleware('auth');
});

Route::group(['prefix'=> 'generos'], function(){
    Route::get('/', [GeneroController::class, 'index'])->name('generos.index');

    Route::get('/create', [GeneroController::class, 'create'])->name('generos.create')->middleware(['auth', 'admin']);

    Route::post('/store', [GeneroController::class,'store'])->name('generos.store')->middleware(['auth', 'admin']);

    Route::get('/eliminados', [GeneroController::class, 'deleted'])->name('generos.deleted')->middleware(['auth', 'admin']);

    Route::post('/{id}/restaurar', [GeneroController::class, 'restore'])->name('generos.restore')->middleware(['auth', 'admin']);

    Route::get('/{id}', [GeneroController::class,'show'])->name('generos.show');

    Route::get('/{id}/edit', [GeneroController::class, 'edit'])->name('generos.edit')->middleware(['auth', 'admin']);

    Route::patch('/{id}', [GeneroController::class, 'update'])->name('generos.update')->middleware(['auth', 'admin']);

    Route::delete('/{id}', [GeneroController::class, 'destroy'])->name('generos.destroy')->middleware(['auth', 'admin']);
});

Route::group(['prefix' => 'directores'], function (){
    Route::get('/', [DirectorController::class, 'index'])->name('directores.index');

    Route::get('/create', [DirectorController::class, 'create'])->name('directores.create')->middleware(['auth', 'admin']);

    Route::post('/store', [DirectorController::class,'store'])->name('directores.store')->middleware(['auth', 'admin']);

    Route::get('/eliminados', [DirectorController::class, 'deleted'])->name('directores.deleted')->middleware(['auth', 'admin']);

    Route::post('/{id}/restaurar', [DirectorController::class,'restore'])->name('directores.restore')->middleware(['auth', 'admin']);

    Route::get('/{id}', [DirectorController::class,'show'])->name('directores.show');

    Route::get('/{id}/edit', [DirectorController::class, 'edit'])->name('directores.edit')->middleware(['auth', 'admin']);

    Route::patch('/{id}', [DirectorController::class, 'update'])->name('directores.update')->middleware(['auth', 'admin']);

    Route::delete('/{id}', [DirectorController::class, 'destroy'])->name('directores.destroy')->middleware(['auth', 'admin']);
});

Route::group(['prefix' => 'actores'], function (){
    Route::get('/', [ActorController::class, 'index'])->name('actores.index');

    Route::get('/create', [ActorController::class, 'create'])->name('actores.create')->middleware(['auth', 'admin']);

    Route::post('/store', [ActorController::class,'store'])->name('actores.store')->middleware(['auth', 'admin']);

    Route::get('/eliminados', [ActorController::class, 'deleted'])->name('actores.deleted')->middleware(['auth', 'admin']);

    Route::post('/{id}/restaurar', [ActorController::class,'restore'])->name('actores.restore')->middleware(['auth', 'admin']);

    Route::get('/{id}', [ActorController::class,'show'])->name('actores.show');

    Route::get('/{id}/edit', [ActorController::class, 'edit'])->name('actores.edit')->middleware(['auth', 'admin']);

    Route::patch('/{id}', [ActorController::class, 'update'])->name('actores.update')->middleware(['auth', 'admin']);

    Route::delete('/{id}', [ActorController::class, 'destroy'])->name('actores.destroy')->middleware(['auth', 'admin']);
});

Route::group(['prefix' => 'premios'], function (){
    Route::get('/', [PremioController::class, 'index'])->name('premios.index');

    Route::get('/create', [PremioController::class, 'create'])->name('premios.create')->middleware(['auth', 'admin']);

    Route::post('/store', [PremioController::class,'store'])->name('premios.store')->middleware(['auth', 'admin']);

    Route::get('/eliminados', [PremioController::class, 'deleted'])->name('premios.deleted')->middleware(['auth', 'admin']);

    Route::post('/{id}/restaurar', [PremioController::class,'restore'])->name('premios.restore')->middleware(['auth', 'admin']);

    Route::get('/{id}', [PremioController::class,'show'])->name('premios.show');

    Route::get('/{id}/edit', [PremioController::class, 'edit'])->name('premios.edit')->middleware(['auth', 'admin']);

    Route::patch('/{id}', [PremioController::class, 'update'])->name('premios.update')->middleware(['auth', 'admin']);

    Route::delete('/{id}', [PremioController::class, 'destroy'])->name('premios.destroy')->middleware(['auth', 'admin']);
});



Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard')->middleware('auth', 'user');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Rutas para las secciones del dashboard
    Route::get('/admin/peliculas', [AdminDashboardController::class, 'peliculas'])->name('admin.peliculas');
    Route::get('/admin/generos', [AdminDashboardController::class, 'generos'])->name('admin.generos');
    Route::get('/admin/directores', [AdminDashboardController::class, 'directores'])->name('admin.directores');
    Route::get('/admin/actores', [AdminDashboardController::class, 'actores'])->name('admin.actores');
    Route::get('/admin/premios', [AdminDashboardController::class, 'premios'])->name('admin.premios');
    Route::get('/resenas', [AdminDashboardController::class, 'resenas'])->name('admin.resenas');
    Route::get('/resenas/{pelicula}', [AdminDashboardController::class, 'resenasPorPelicula'])->name('admin.resenas.show');
    Route::delete('/resenas/{resena}', [ResenaController::class, 'destroy'])->name('admin.resenas.destroy');
});


/*Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});*/

require __DIR__.'/auth.php';
