<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Premio extends Model
{
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
