<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Director",
 *     @OA\Property(property="id", type="integer", description="ID único del director"),
 *     @OA\Property(property="nombre", type="string", description="Nombre del director"),
 *     @OA\Property(property="fecha_nac", type="string", format="date", description="Fecha de nacimiento del director"),
 *     @OA\Property(property="biografia", type="string", description="Biografía del director")
 * )
 */

class Director extends Model
{
    use HasFactory;
    use SoftDeletes;
    public static string $IMAGEN_DEFAULT = 'https://placehold.co/230x340';
    protected $table = 'directores';

    protected $fillable = [
        'nombre',
        'fecha_nac',
        'lugar_nac',
        'biografia',
        'inicio_actividad',
        'fin_actividad',
        'activo',
        'imagen',
    ];

    protected $casts = [
        'fecha_nac' => 'date',
        'inicio_actividad' => 'integer',
        'fin_actividad' => 'integer',
        'activo' => 'boolean',
    ];

    public function scopeSearch($query, $search)
    {

        return $query->whereRaw('LOWER(nombre) LIKE ?', ["%" . strtolower($search) . "%"]);
    }

    public function getAniosEdadAttribute()
    {

        return (int) Carbon::parse($this->fecha_nac)->diffInYears(Carbon::now());
    }

    public function getAniosActivoAttribute()
    {
        if (!$this->inicio_actividad) {
            return null;
        }

        $fin = $this->fin_actividad ?? Carbon::now()->year;
        return $fin - $this->inicio_actividad;
    }

    public function peliculas()
    {

        return $this->hasMany(Pelicula::class, 'director_id');
    }

    public function premios()
    {

        return $this->morphMany(Premio::class, 'entidad');
    }
}
