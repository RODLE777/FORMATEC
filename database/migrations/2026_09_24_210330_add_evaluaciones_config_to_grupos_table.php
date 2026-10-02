<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->json('evaluaciones_config')->nullable()->after('numero_evaluaciones');
        });

        // Rellenar grupos existentes con una config por defecto (pesos iguales)
        foreach (DB::table('grupos')->get() as $grupo) {
            $n = (int) ($grupo->numero_evaluaciones ?: 3);
            $base = round(100 / $n, 2);
            $config = [];
            $acum = 0;
            for ($i = 1; $i <= $n; $i++) {
                $peso = ($i === $n) ? round(100 - $acum, 2) : $base;
                $config[] = ['nombre' => "Evaluación {$i}", 'porcentaje' => $peso];
                $acum += $peso;
            }
            DB::table('grupos')->where('id', $grupo->id)->update([
                'evaluaciones_config' => json_encode($config),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropColumn('evaluaciones_config');
        });
    }
};