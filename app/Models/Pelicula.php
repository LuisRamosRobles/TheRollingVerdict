<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelicula extends Model
{
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

    // Relación tabla Reseñas
    public function resenas()
    {
        return $this->hasMany(Resena::class, "pelicula_id");
    }

    public function generos()
    {
        return $this->belongsToMany(Genero::class,
            'genero_pelicula', 'pelicula_id', 'genero_id');
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
