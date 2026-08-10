<?php

namespace App\Console\Commands;

use App\Models\Grupo;
use Illuminate\Console\Command;


class ActualizarEstadosGrupos extends Command
{
    protected $signature = 'formatec:actualizar-estados-grupos';

    protected $description = 'Actualiza el estado de los grupos (PLANIFICADO/EN_CURSO/FINALIZADO) segun sus fechas';

    public function handle(): int
    {
        $hoy = now()->toDateString();

        $iniciados = Grupo::where('estado', 'PLANIFICADO')
            ->whereNotNull('fecha_inicio')
            ->where('fecha_inicio', '<=', $hoy)
            ->update(['estado' => 'EN_CURSO']);

        $finalizados = Grupo::whereIn('estado', ['PLANIFICADO', 'EN_CURSO'])
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '<', $hoy)
            ->update(['estado' => 'FINALIZADO']);

        $this->info("Grupos pasados a EN_CURSO: {$iniciados}. Grupos pasados a FINALIZADO: {$finalizados}.");

        return self::SUCCESS;
    }
}
