<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('fecha_inscripcion');
            $table->enum('estado', ['ACTIVA', 'RETIRADA', 'FINALIZADA'])->default('ACTIVA');
            $table->enum('resultado_final', ['EN_CURSO', 'GRADUADO', 'DESERTADO', 'REPROBADO'])->default('EN_CURSO');
            $table->decimal('nota_final', 4, 2)->nullable(); // solo via trigger, no escribir desde Eloquent
            $table->date('fecha_finalizacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['estudiante_id', 'grupo_id'], 'uq_inscripcion');
            $table->index('resultado_final', 'idx_inscripcion_resultado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
