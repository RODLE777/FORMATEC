<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete()->cascadeOnUpdate();
            $table->smallInteger('numero_sesion')->unsigned();
            $table->date('fecha');
            $table->string('tema', 200)->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();

            $table->unique(['grupo_id', 'numero_sesion'], 'uq_sesion');
        });

        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('sesion_id')->constrained('sesiones')->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('asistio')->default(false);
            $table->string('observacion', 255)->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->unique(['inscripcion_id', 'sesion_id'], 'uq_asistencia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
        Schema::dropIfExists('sesiones');
    }
};
