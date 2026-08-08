<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
        });

        Schema::create('municipios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departamento_id')->constrained('departamentos')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('nombre', 100);
            $table->unique(['departamento_id', 'nombre'], 'uq_municipio');
        });

        Schema::create('distritos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('municipio_id')->constrained('municipios')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('nombre', 100);
            $table->unique(['municipio_id', 'nombre'], 'uq_distrito');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distritos');
        Schema::dropIfExists('municipios');
        Schema::dropIfExists('departamentos');
    }
};
