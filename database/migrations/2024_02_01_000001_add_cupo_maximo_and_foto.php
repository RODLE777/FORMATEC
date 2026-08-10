<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->smallInteger('cupo_maximo')->unsigned()->nullable()->after('numero_evaluaciones');
        });

        Schema::table('estudiantes', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->after('usuario_certiport');
        });
    }

    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            $table->dropColumn('cupo_maximo');
        });

        Schema::table('estudiantes', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};
