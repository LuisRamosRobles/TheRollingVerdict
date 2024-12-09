<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Premio",
 *     @OA\Property(property="id", type="integer", description="ID único del premio"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del premio"),
 *     @OA\Property(property="categoria", type="string", description="Categoría del premio"),
 *     @OA\Property(property="anio", type="integer", description="Año en el que se otorgó el premio")
 * )
 */

class Premio extends Model
{
    use HasFactory;
    use SoftDeletes;
    public static string $IMAGEN_DEFAULT = 'https://placehold.co/230x340';
    protected $table = 'premios';

    protected $fillable = [
        'nombre',
        'categoria',
        'anio',
        'pelicula_id',
        'entidad_type',
        'entidad_id',
        'imagen'
    ];

    public function scopeSearch($query, $search)
    {
        return $query->whereRaw('LOWER(nombre) LIKE ?', ["%" . strtolower($search) . "%"]);
    }

    public function entidad()
    {
        return $this->morphTo();
    }

    public function pelicula()
    {
        return $this->belongsTo(Pelicula::class);
    }
}
