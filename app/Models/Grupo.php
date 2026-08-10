<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupos';
    use HasFactory;

    protected $fillable = [
        'curso_id', 'profesor_id', 'codigo_grupo', 'anio', 'mes',
        'fecha_inicio', 'fecha_fin', 'duracion_horas', 'horario', 'lugar',
        'numero_evaluaciones', 'cupo_maximo', 'plataforma_certificacion', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    /** Cupos disponibles, o null si el grupo no tiene limite definido. */
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
