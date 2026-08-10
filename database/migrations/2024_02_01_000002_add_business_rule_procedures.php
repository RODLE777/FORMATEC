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

        DB::unprepared('DROP TRIGGER IF EXISTS trg_evaluacion_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_evaluacion_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_evaluacion_after_delete');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_recalcular_inscripcion');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_evaluar_desercion');

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

        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE sp_evaluar_desercion(
                IN p_inscripcion_id BIGINT UNSIGNED,
                IN p_sesion_id BIGINT UNSIGNED,
                IN p_asistio TINYINT
            )
            BEGIN
                DECLARE v_grupo_id BIGINT UNSIGNED;
                DECLARE v_resultado_actual VARCHAR(20);
                DECLARE v_faltas INT;

                IF p_asistio = 0 THEN
                    SELECT resultado_final INTO v_resultado_actual FROM inscripciones WHERE id = p_inscripcion_id;

                    IF v_resultado_actual = 'EN_CURSO' THEN
                        SELECT s.grupo_id INTO v_grupo_id FROM sesiones s WHERE s.id = p_sesion_id;

                        SELECT COUNT(*) INTO v_faltas FROM (
                            SELECT a.asistio
                            FROM asistencias a
                            JOIN sesiones s2 ON s2.id = a.sesion_id
                            WHERE a.inscripcion_id = p_inscripcion_id AND s2.grupo_id = v_grupo_id
                            ORDER BY s2.numero_sesion DESC
                            LIMIT 3
                        ) ultimas
                        WHERE ultimas.asistio = 0;

                        IF v_faltas >= 3 THEN
                            UPDATE inscripciones
                            SET resultado_final = 'DESERTADO', estado = 'RETIRADA', fecha_finalizacion = CURDATE()
                            WHERE id = p_inscripcion_id AND resultado_final = 'EN_CURSO';
                        END IF;
                    END IF;
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_evaluacion_after_insert
            AFTER INSERT ON evaluaciones
            FOR EACH ROW
            BEGIN
                CALL sp_recalcular_inscripcion(NEW.inscripcion_id);
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_evaluacion_after_update
            AFTER UPDATE ON evaluaciones
            FOR EACH ROW
            BEGIN
                CALL sp_recalcular_inscripcion(NEW.inscripcion_id);
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_evaluacion_after_delete
            AFTER DELETE ON evaluaciones
            FOR EACH ROW
            BEGIN
                CALL sp_recalcular_inscripcion(OLD.inscripcion_id);
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_asistencia_desercion_insert
            AFTER INSERT ON asistencias
            FOR EACH ROW
            BEGIN
                CALL sp_evaluar_desercion(NEW.inscripcion_id, NEW.sesion_id, NEW.asistio);
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_asistencia_desercion_update
            AFTER UPDATE ON asistencias
            FOR EACH ROW
            BEGIN
                CALL sp_evaluar_desercion(NEW.inscripcion_id, NEW.sesion_id, NEW.asistio);
            END
        SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trg_asistencia_desercion_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_asistencia_desercion_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_evaluacion_after_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_evaluacion_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_evaluacion_after_insert');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_evaluar_desercion');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_recalcular_inscripcion');
    }
};
