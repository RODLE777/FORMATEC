<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE VIEW vista_totalon_mensual AS
            SELECT
                g.anio,
                g.mes,
                c.nombre AS curso,
                COUNT(DISTINCT g.id) AS numero_de_cursos,
                COUNT(DISTINCT CASE WHEN e.sexo = 'MASCULINO' THEN i.id END) AS iniciaron_masculino,
                COUNT(DISTINCT CASE WHEN e.sexo = 'FEMENINO' THEN i.id END) AS iniciaron_femenino,
                COUNT(DISTINCT i.id) AS iniciaron_total,
                COUNT(DISTINCT CASE WHEN i.resultado_final = 'DESERTADO' AND e.sexo = 'MASCULINO' THEN i.id END) AS desertados_masculino,
                COUNT(DISTINCT CASE WHEN i.resultado_final = 'DESERTADO' AND e.sexo = 'FEMENINO' THEN i.id END) AS desertados_femenino,
                COUNT(DISTINCT CASE WHEN i.resultado_final = 'DESERTADO' THEN i.id END) AS desertados_total,
                COUNT(DISTINCT CASE WHEN i.resultado_final = 'GRADUADO' AND e.sexo = 'MASCULINO' THEN i.id END) AS graduados_masculino,
                COUNT(DISTINCT CASE WHEN i.resultado_final = 'GRADUADO' AND e.sexo = 'FEMENINO' THEN i.id END) AS graduados_femenino,
                COUNT(DISTINCT CASE WHEN i.resultado_final = 'GRADUADO' THEN i.id END) AS graduados_total
            FROM grupos g
            JOIN cursos c ON c.id = g.curso_id
            JOIN inscripciones i ON i.grupo_id = g.id
            JOIN estudiantes e ON e.id = i.estudiante_id
            GROUP BY g.anio, g.mes, c.nombre
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE VIEW vista_totalon_anual AS
            SELECT
                anio,
                curso,
                SUM(numero_de_cursos) AS numero_de_cursos,
                SUM(iniciaron_masculino) AS iniciaron_masculino,
                SUM(iniciaron_femenino) AS iniciaron_femenino,
                SUM(iniciaron_total) AS iniciaron_total,
                SUM(desertados_masculino) AS desertados_masculino,
                SUM(desertados_femenino) AS desertados_femenino,
                SUM(desertados_total) AS desertados_total,
                SUM(graduados_masculino) AS graduados_masculino,
                SUM(graduados_femenino) AS graduados_femenino,
                SUM(graduados_total) AS graduados_total
            FROM vista_totalon_mensual
            GROUP BY anio, curso
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE VIEW vista_estudiantes AS
            SELECT
                es.*,
                TIMESTAMPDIFF(YEAR, es.fecha_nacimiento, CURDATE()) AS edad,
                (TIMESTAMPDIFF(YEAR, es.fecha_nacimiento, CURDATE()) < 18) AS es_menor_edad,
                d.nombre AS departamento,
                m.nombre AS municipio,
                di.nombre AS distrito
            FROM estudiantes es
            LEFT JOIN departamentos d ON d.id = es.departamento_id
            LEFT JOIN municipios m ON m.id = es.municipio_id
            LEFT JOIN distritos di ON di.id = es.distrito_id
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE VIEW vista_porcentaje_asistencia AS
            SELECT
                a.inscripcion_id,
                COUNT(*) AS total_sesiones,
                SUM(a.asistio) AS sesiones_asistidas,
                ROUND(SUM(a.asistio) / COUNT(*) * 100, 2) AS porcentaje_asistencia
            FROM asistencias a
            GROUP BY a.inscripcion_id
        SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP VIEW IF EXISTS vista_porcentaje_asistencia');
        DB::unprepared('DROP VIEW IF EXISTS vista_estudiantes');
        DB::unprepared('DROP VIEW IF EXISTS vista_totalon_anual');
        DB::unprepared('DROP VIEW IF EXISTS vista_totalon_mensual');
    }
};
