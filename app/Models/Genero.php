<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genero extends Model
{
    use SoftDeletes;
    public static string $IMAGEN_DEFAULT = 'https://placehold.co/380x220';
    protected  $table = 'generos';

    protected $fillable = [
        'nombre',
        'imagen'
    ];

    public function scopeSearch($query, $search) {
        return $query->whereRaw('LOWER(nombre) LIKE ?', ["%" . strtolower($search) . "%"]);
    }

    public function peliculas()
    {
        return $this->belongsToMany(Pelicula::class, 'genero_pelicula', 'genero_id', 'pelicula_id');
    }
}
