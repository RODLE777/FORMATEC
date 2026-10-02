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

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_recalcular_inscripcion');

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_recalcular_inscripcion(IN p_inscripcion_id BIGINT UNSIGNED)
            BEGIN
                DECLARE v_promedio DECIMAL(4,2);
                DECLARE v_cantidad_notas INT;
                DECLARE v_total_esperado INT;
                DECLARE v_resultado_actual VARCHAR(20);
                DECLARE v_config JSON;

                SELECT g.numero_evaluaciones, g.evaluaciones_config, i.resultado_final
                INTO v_total_esperado, v_config, v_resultado_actual
                FROM inscripciones i
                JOIN grupos g ON g.id = i.grupo_id
                WHERE i.id = p_inscripcion_id;

                SELECT COUNT(*) INTO v_cantidad_notas
                FROM evaluaciones WHERE inscripcion_id = p_inscripcion_id;

                IF v_config IS NULL OR JSON_LENGTH(v_config) = 0 THEN
                    -- Fallback: promedio simple (compatibilidad)
                    SELECT COALESCE(ROUND(AVG(nota), 2), 0) INTO v_promedio
                    FROM evaluaciones WHERE inscripcion_id = p_inscripcion_id;
                ELSE
                    -- Promedio ponderado: las evaluaciones sin fila cuentan como 0
                    SELECT ROUND(
                        COALESCE(SUM(
                            COALESCE(
                                (SELECT e.nota FROM evaluaciones e
                                 WHERE e.inscripcion_id = p_inscripcion_id
                                   AND e.numero_evaluacion = jt.numero_evaluacion),
                                0
                            ) * jt.porcentaje / 100
                        ), 0)
                    , 2) INTO v_promedio
                    FROM JSON_TABLE(v_config, '$[*]' COLUMNS (
                        numero_evaluacion FOR ORDINALITY,
                        porcentaje DECIMAL(5,2) PATH '$.porcentaje'
                    )) jt;
                END IF;

                UPDATE inscripciones SET nota_final = v_promedio WHERE id = p_inscripcion_id;

                IF v_resultado_actual = 'EN_CURSO' AND v_cantidad_notas >= v_total_esperado THEN
                    UPDATE inscripciones
                    SET resultado_final = IF(v_promedio >= 6.00, 'GRADUADO', 'REPROBADO'),
                        estado = 'FINALIZADA',
                        fecha_finalizacion = CURDATE()
                    WHERE id = p_inscripcion_id AND resultado_final = 'EN_CURSO';
                END IF;
            END
        SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_recalcular_inscripcion');

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_recalcular_inscripcion(IN p_inscripcion_id BIGINT UNSIGNED)
            BEGIN
                DECLARE v_promedio DECIMAL(4,2);
                DECLARE v_cantidad_notas INT;
                DECLARE v_total_esperado INT;
                DECLARE v_resultado_actual VARCHAR(20);

                SELECT ROUND(AVG(nota), 2), COUNT(*) INTO v_promedio, v_cantidad_notas
                FROM evaluaciones WHERE inscripcion_id = p_inscripcion_id;

                SELECT g.numero_evaluaciones, i.resultado_final
                INTO v_total_esperado, v_resultado_actual
                FROM inscripciones i JOIN grupos g ON g.id = i.grupo_id
                WHERE i.id = p_inscripcion_id;

                UPDATE inscripciones SET nota_final = v_promedio WHERE id = p_inscripcion_id;

                IF v_resultado_actual = 'EN_CURSO' AND v_cantidad_notas >= v_total_esperado THEN
                    UPDATE inscripciones
                    SET resultado_final = IF(v_promedio >= 6.00, 'GRADUADO', 'REPROBADO'),
                        estado = 'FINALIZADA',
                        fecha_finalizacion = CURDATE()
                    WHERE id = p_inscripcion_id AND resultado_final = 'EN_CURSO';
                END IF;
            END
        SQL);
    }
};