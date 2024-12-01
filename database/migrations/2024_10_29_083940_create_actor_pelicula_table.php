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
        Schema::create('actor_pelicula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->references('id')->on('actores')->onDelete('cascade');
            $table->foreignId('pelicula_id')->references('id')->on('peliculas')->onDelete('cascade');
            $table->unique(['actor_id', 'pelicula_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actor_pelicula');
    }
};
