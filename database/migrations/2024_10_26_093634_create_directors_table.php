<?php

use App\Models\Director;
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
        Schema::create('directores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->date('fecha_nac')->nullable();
            $table->string('lugar_nac')->nullable();
            $table->text('biografia')->nullable();
            $table->year('inicio_actividad')->nullable();
            $table->year('fin_actividad')->nullable();
            $table->boolean('activo')->default(true);
            $table->string('imagen')->default(Director::$IMAGEN_DEFAULT);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directores');
    }
};
