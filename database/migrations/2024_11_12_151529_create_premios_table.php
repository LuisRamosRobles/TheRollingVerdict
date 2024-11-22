<?php

use App\Models\Premio;
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
        Schema::create('premios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('categoria');
            $table->year('anio');
            $table->morphs('entidad');
            $table->foreignId('pelicula_id')->nullable()->constrained('peliculas')->onDelete('set null');
            $table->string('imagen')->default(Premio::$IMAGEN_DEFAULT);
            $table->unique(['nombre', 'categoria', 'anio'], 'premios_unicos');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('premios');
    }
};
