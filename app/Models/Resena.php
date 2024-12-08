<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resena extends Model
{
    use HasFactory;

    protected $table = 'resenas';

    protected $fillable = [
        'user_id',
        'pelicula_id',
        'calificacion',
        'comentario'
    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function pelicula ()
    {
        return $this->belongsTo(Pelicula::class, 'pelicula_id');
    }

}
