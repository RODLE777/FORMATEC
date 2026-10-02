<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $table = 'grupos';

    protected $fillable = [
        'curso_id', 'profesor_id', 'codigo_grupo', 'anio', 'mes',
        'fecha_inicio', 'fecha_fin', 'duracion_horas', 'horario', 'lugar',
        'numero_evaluaciones', 'cupo_maximo', 'plataforma_certificacion',
        'estado', 'evaluaciones_config',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio'        => 'date',
            'fecha_fin'           => 'date',
            'evaluaciones_config' => 'array',
        ];
    }

    /**
     * Devuelve la config de evaluaciones del grupo.
     * Si no tiene, genera una por defecto con pesos iguales.
     */
    public function configEvaluaciones(): array
    {
        $config = $this->evaluaciones_config;

        if (is_array($config) && count($config) > 0) {
            return $config;
        }

        return self::generarConfigPorDefecto((int) ($this->numero_evaluaciones ?: 3));
    }

    /** Genera N evaluaciones con pesos iguales que suman 100.00 */
    public static function generarConfigPorDefecto(int $n): array
    {
        $n = max(1, min(50, $n));
        $base = round(100 / $n, 2);
        $config = [];
        $acum = 0;

        for ($i = 1; $i <= $n; $i++) {
            $peso = ($i === $n) ? round(100 - $acum, 2) : $base;
            $config[] = ['nombre' => "Evaluación {$i}", 'porcentaje' => $peso];
            $acum += $peso;
        }

        return $config;
    }

    /** True si la config actual suma exactamente 100.00 */
    public function configValida(): bool
    {
        $suma = round(array_sum(array_column($this->configEvaluaciones(), 'porcentaje')), 2);
        return $suma === 100.00;
    }

    public function cuposDisponibles(): ?int
    {
        if ($this->cupo_maximo === null) {
            return null;
        }

        return max(0, $this->cupo_maximo - $this->inscripciones()->where('estado', 'ACTIVA')->count());
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function sesiones()
    {
        return $this->hasMany(Sesion::class);
    }

    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'inscripciones')
            ->withPivot(['id', 'estado', 'resultado_final', 'nota_final', 'fecha_inscripcion']);
    }
}