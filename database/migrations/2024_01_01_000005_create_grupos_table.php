<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_id')->constrained('cursos')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('profesor_id')->nullable()->constrained('profesores')->nullOnDelete()->cascadeOnUpdate();
            $table->string('codigo_grupo', 50);
            $table->smallInteger('anio')->unsigned();
            $table->tinyInteger('mes')->unsigned();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->smallInteger('duracion_horas')->unsigned()->nullable();
            $table->string('horario', 100)->nullable();
            $table->string('lugar', 150)->nullable();
            $table->tinyInteger('numero_evaluaciones')->unsigned()->default(3);
            $table->string('plataforma_certificacion', 50)->nullable();
            $table->enum('estado', ['PLANIFICADO', 'EN_CURSO', 'FINALIZADO', 'CANCELADO'])->default('PLANIFICADO');
            $table->timestamps();

            $table->unique(['codigo_grupo', 'anio'], 'uq_grupo_codigo');
            $table->index(['anio', 'mes'], 'idx_grupo_anio_mes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
