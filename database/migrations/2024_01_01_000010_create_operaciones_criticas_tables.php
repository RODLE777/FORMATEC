<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('importaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('archivo_nombre', 255);
            $table->enum('tipo', ['ESTUDIANTES', 'CURSOS', 'NOTAS', 'HISTORICO']);
            $table->unsignedInteger('total_filas')->default(0);
            $table->unsignedInteger('filas_importadas')->default(0);
            $table->unsignedInteger('filas_error')->default(0);
            $table->enum('estado', ['PENDIENTE', 'PROCESANDO', 'COMPLETADO', 'ERROR'])->default('PENDIENTE');
            $table->json('detalle_errores')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('nombre_archivo', 255);
            $table->string('ruta', 500)->nullable();
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->enum('tipo', ['MANUAL', 'PROGRAMADO', 'PRE_RESTAURACION'])->default('MANUAL');
            $table->enum('estado', ['COMPLETADO', 'ERROR'])->default('COMPLETADO');
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        Schema::create('reportes_generados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('tipo_reporte', 100);
            $table->json('parametros')->nullable();
            $table->string('archivo', 500)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes_generados');
        Schema::dropIfExists('backups');
        Schema::dropIfExists('importaciones');
    }
};
