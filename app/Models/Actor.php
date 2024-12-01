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

        $fin = $this->fin_actividad ?? Carbon::now()->year; // Usa el año actual si no hay fecha de retiro
        return $fin - $this->inicio_actividad;
    }

    public function peliculas()
    {
        return $this->belongsToMany(Pelicula::class,
        'actor_pelicula', 'actor_id', 'pelicula_id');
    }

    public function premios()
    {
        return $this->morphMany(Premio::class, 'entidad');
    }
}
