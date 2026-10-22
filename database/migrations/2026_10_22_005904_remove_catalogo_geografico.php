<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Eliminar la vista que depende de las tablas geográficas
        DB::statement('DROP VIEW IF EXISTS vista_estudiantes');

        // 2. Quitar llaves foráneas y columnas de la tabla estudiantes
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->dropForeign(['departamento_id']);
            $table->dropForeign(['municipio_id']);
            $table->dropForeign(['distrito_id']);
            
            $table->dropColumn(['departamento_id', 'municipio_id', 'distrito_id']);
        });

        // 3. Eliminar las tablas (en orden inverso para respetar las llaves foráneas)
        Schema::dropIfExists('distritos');
        Schema::dropIfExists('municipios');
        Schema::dropIfExists('departamentos');

        // 4. Recrear la vista (por si la usas en reportes) pero SIN los campos geográficos
        DB::statement("
            CREATE OR REPLACE VIEW vista_estudiantes AS
            SELECT 
                id, codigo_formatec, nombres, apellidos, sexo, fecha_nacimiento, 
                dui, nit, correo, telefono_fijo, telefono_celular, direccion, 
                comunidad, profesion_oficio, nivel_estudio, enfermedades, 
                usuario_certiport, activo, created_at, updated_at,
                TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) AS edad,
                (TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE()) < 18) AS es_menor_edad
            FROM estudiantes
        ");
    }

    public function down(): void
    {
        // Eliminación definitiva, no hay vuelta atrás.
    }
};