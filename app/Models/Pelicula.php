<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;


/**
 * @OA\Schema(
 *     schema="Pelicula",
 *     required={"titulo", "estreno", "director_id", "sinopsis"},
 *     @OA\Property(property="id", type="integer", description="ID único de la película"),
 *     @OA\Property(property="titulo", type="string", description="Título de la película"),
 *     @OA\Property(property="generos", type="array", @OA\Items(ref="#/components/schemas/Genero")),
 *     @OA\Property(property="estreno", type="string", format="date", description="Fecha de estreno"),
 *     @OA\Property(property="director", ref="#/components/schemas/Director"),
 *     @OA\Property(property="sinopsis", type="string", description="Sinopsis de la película"),
 *     @OA\Property(property="reparto", type="array", @OA\Items(ref="#/components/schemas/Actor")),
 *     @OA\Property(property="imagen", type="string", description="URL de la imagen"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de última actualización")
 * )
 */

class Pelicula extends Model
{
    use HasFactory;
    use SoftDeletes;
    public static string $IMAGEN_DEFAULT = 'https://placehold.co/230x340';
    protected $table = 'peliculas';

    protected $fillable = [
        'titulo',
        'estreno',
        'director_id',
        'sinopsis',
        'reparto',
        'imagen'
    ];


    public function scopeSearch($query, $search)
    {
        return $query->whereRaw('LOWER(titulo) LIKE ?', ["%" . strtolower($search) . "%"]);
    }

    public function getPromedioCalificacionAttribute()
    {
        $cacheKey = "pelicula_{$this->id}_promedio_calificacion";

        return Cache::remember($cacheKey, now()->addMinutes(10), function (){
            return $this->resenas()->avg('calificacion') ?: 0;
        });

    }


    public function resenas()
    {
        return $this->hasMany(Resena::class, "pelicula_id");
    }

    public function generos()
    {
        return $this->belongsToMany(Genero::class,
            'genero_pelicula', 'pelicula_id', 'genero_id');
    }

    public function actores()
    {
        return $this->belongsToMany(Actor::class,
            'actor_pelicula', 'pelicula_id', 'actor_id');
    }

    public function director ()
    {
        return $this->belongsTo(Director::class, 'director_id');
    }

    public function premios()
    {
        return $this->morphMany(Premio::class, 'entidad');
    }


}
