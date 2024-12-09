<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Resena",
 *     type="object",
 *     required={"id", "calificacion", "comentario", "user_id", "pelicula_id"},
 *     @OA\Property(property="id", type="integer", description="ID único de la reseña"),
 *     @OA\Property(property="calificacion", type="integer", description="Calificación de la película (1 a 5)"),
 *     @OA\Property(property="comentario", type="string", description="Comentario del usuario", nullable=true),
 *     @OA\Property(property="user_id", type="integer", description="ID del usuario que dejó la reseña"),
 *     @OA\Property(property="pelicula_id", type="integer", description="ID de la película asociada a la reseña"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Fecha de creación de la reseña"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Fecha de última actualización de la reseña")
 * )
 */

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
