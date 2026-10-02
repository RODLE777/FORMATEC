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
            CREATE TRIGGER trg_evaluacion_after_insert
            AFTER INSERT ON evaluaciones
            FOR EACH ROW
            BEGIN
                UPDATE inscripciones
                SET nota_final = (SELECT ROUND(AVG(nota), 2) FROM evaluaciones WHERE inscripcion_id = NEW.inscripcion_id)
                WHERE id = NEW.inscripcion_id;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_evaluacion_after_update
            AFTER UPDATE ON evaluaciones
            FOR EACH ROW
            BEGIN
                UPDATE inscripciones
                SET nota_final = (SELECT ROUND(AVG(nota), 2) FROM evaluaciones WHERE inscripcion_id = NEW.inscripcion_id)
                WHERE id = NEW.inscripcion_id;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_evaluacion_after_delete
            AFTER DELETE ON evaluaciones
            FOR EACH ROW
            BEGIN
                UPDATE inscripciones
                SET nota_final = (SELECT ROUND(AVG(nota), 2) FROM evaluaciones WHERE inscripcion_id = OLD.inscripcion_id)
                WHERE id = OLD.inscripcion_id;
            END
        SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trg_estudiante_codigo');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_evaluacion_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_evaluacion_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_evaluacion_after_delete');
    }
};
