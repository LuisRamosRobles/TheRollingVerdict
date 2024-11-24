<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Actor extends Model
{
    use SoftDeletes;
    public static string $IMAGEN_DEFAULT = 'https://placehold.co/230x340';
    protected $table = 'actores';

    protected $fillable = [
        'nombre',
        'apellido',
        'fecha_nac',
        'lugar_nac',
        'biografia',
        'inicio_actividad',
        'activo',
        'imagen',
    ];

    protected $casts = [
        'fecha_nac' => 'date',
        'inicio_actividad' => 'date',
        'activo' => 'boolean',
    ];

    public function scopeSearch($query, $search) {

        return $query->whereRaw('LOWER(nombre) LIKE ?', ["%" . strtolower($search) . "%"]);
    }

    public function getAniosEdadAttribute() {

        return (int) Carbon::parse($this->fecha_nac)->diffInYears(Carbon::now());
    }

    public function getAniosActivoAttribute() {

        return (int) Carbon::parse($this->inicio_actividad)->diffInYears(Carbon::now());
    }

    public function peliculas() {

        return $this->hasMany(Pelicula::class, 'director_id');
    }

    public function premios() {

        return $this->morphMany(Premio::class, 'entidad');
    }
}
