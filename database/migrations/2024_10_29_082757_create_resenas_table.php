<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resenas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('pelicula_id');
            $table->integer('calificacion');
            $table->text('comentario');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pelicula_id')->references('id')->on('peliculas')->onDelete('cascade');
            $table->unique(['user_id', 'pelicula_id']);  //Restricción de unívocidad para evitar duplicados.
            $table->softDeletes();  // Campo deleted_at para softDeletes.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};
