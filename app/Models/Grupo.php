<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $fillable = [
        'curso_id', 'profesor_id', 'codigo_grupo', 'anio', 'mes',
        'fecha_inicio', 'fecha_fin', 'duracion_horas', 'horario', 'lugar',
        'numero_evaluaciones', 'plataforma_certificacion', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
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
