<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('numero_evaluacion')->unsigned();
            $table->string('nombre_evaluacion', 100)->nullable();
            $table->decimal('nota', 4, 2)->default(0);
            $table->date('fecha_evaluacion')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->unique(['inscripcion_id', 'numero_evaluacion'], 'uq_evaluacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones');
    }
};
