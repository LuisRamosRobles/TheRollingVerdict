<?php

use App\Models\Pelicula;
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
        Schema::create('peliculas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->date('estreno');
            $table->text('sinopsis');
            $table->string('reparto');
            $table->string('imagen')->default(Pelicula::$IMAGEN_DEFAULT);
            $table->foreignId('director_id')->constrained('directores')->onDelete('cascade');
            $table->softDeletes();  // Campo deleted_at para softDeletes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peliculas');
    }
};
