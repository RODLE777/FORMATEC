<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_formatec', 20)->nullable()->unique();
            $table->string('nombres', 150);
            $table->string('apellidos', 150);
            $table->enum('sexo', ['MASCULINO', 'FEMENINO']);
            $table->date('fecha_nacimiento');
            $table->string('dui', 15)->nullable()->unique();
            $table->string('nit', 20)->nullable();
            $table->string('correo', 150)->nullable();
            $table->string('telefono_fijo', 20)->nullable();
            $table->string('telefono_celular', 20)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('municipio_id')->nullable()->constrained('municipios')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('distrito_id')->nullable()->constrained('distritos')->nullOnDelete()->cascadeOnUpdate();
            $table->string('comunidad', 150)->nullable();
            $table->string('profesion_oficio', 150)->nullable();
            $table->string('nivel_estudio', 100)->nullable();
            $table->text('enfermedades')->nullable();
            $table->string('usuario_certiport', 100)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['apellidos', 'nombres'], 'idx_estudiante_nombre');
        });

        Schema::create('encargados_menor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nombre_completo', 200);
            $table->string('parentesco', 50)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encargados_menor');
        Schema::dropIfExists('estudiantes');
    }
};
