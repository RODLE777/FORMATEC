<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            
            // 1. Quitar llaves foráneas de geografía (con try-catch por seguridad)
            if (Schema::hasColumn('estudiantes', 'departamento_id')) {
                try { $table->dropForeign(['departamento_id']); } catch (\Exception $e) {}
            }
            if (Schema::hasColumn('estudiantes', 'municipio_id')) {
                try { $table->dropForeign(['municipio_id']); } catch (\Exception $e) {}
            }
            if (Schema::hasColumn('estudiantes', 'distrito_id')) {
                try { $table->dropForeign(['distrito_id']); } catch (\Exception $e) {}
            }

            // 2. Lista de columnas que SÍ vamos a borrar
            // (Nota: 'usuario_certiport' NO está en esta lista, se queda)
            $columnas = [
                'nit', 
                'telefono_fijo', 
                'departamento_id', 
                'municipio_id', 
                'distrito_id', 
                'comunidad', 
                'enfermedades', 
                'foto'
            ];

            // 3. Eliminar las columnas una por una si existen
            foreach ($columnas as $columna) {
                if (Schema::hasColumn('estudiantes', $columna)) {
                    $table->dropColumn($columna);
                }
            }
        });
    }

    public function down(): void
    {
        // No hay vuelta atrás
    }
};