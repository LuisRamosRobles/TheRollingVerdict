<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resena extends Model
{
    use SoftDeletes;

    protected $table = 'resenas';

    protected $fillable = [
        'user_id',
        'pelicula_id',
        'calificacion',
        'comentario'
    ];

    // Relación Tabla Usuarios

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación Tabla Películas
    public function pelicula ()
    {
        return $this->belongsTo(Pelicula::class, 'pelicula_id');
    }

}
