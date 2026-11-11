
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kiosco_autorizaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('estudiante_id')
                ->nullable()
                ->constrained('estudiantes')
                ->nullOnDelete();

            $table->enum('accion', [
                'RECUPERAR_EXISTENTE',
                'AUTORIZAR_NUEVO',
                'ACTUALIZAR_DUI'
            ]);

            $table->string('motivo', 500);
            $table->json('datos_verificacion')->nullable();

            $table->timestamps();

            $table->index(['accion', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kiosco_autorizaciones');
    }
};
