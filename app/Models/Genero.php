<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Genero",
 *     @OA\Property(property="id", type="integer", description="ID único del género"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del género")
 * )
 */

class Genero extends Model
{
    use HasFactory;
    use SoftDeletes;
    public static string $IMAGEN_DEFAULT = 'https://placehold.co/380x220';
    protected  $table = 'generos';

    protected $fillable = [
        'nombre',
        'imagen'
    ];

    public function scopeSearch($query, $search)
    {
        return $query->whereRaw('LOWER(nombre) LIKE ?', ["%" . strtolower($search) . "%"]);
    }

    public function peliculas()
    {
        return $this->belongsToMany(Pelicula::class, 'genero_pelicula', 'genero_id', 'pelicula_id');
    }
}
