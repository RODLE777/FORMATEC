<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $table = 'inscripciones';

    protected $fillable = [
        'estudiante_id',
        'grupo_id',
        'fecha_inscripcion',
        'estado',
        'resultado_final',
        'fecha_finalizacion',
        'observaciones',
    ];

    protected $guarded = [
        'id',
        'nota_final',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inscripcion' => 'date',
            'fecha_finalizacion' => 'date',
            'nota_final' => 'decimal:2',
        ];
    }

    public function estudiante()
    {
        return $this->belongsTo(
            Estudiante::class,
            'estudiante_id'
        );
    }

    public function grupo()
    {
        return $this->belongsTo(
            Grupo::class,
            'grupo_id'
        );
    }

    public function evaluaciones()
    {
        return $this->hasMany(
            Evaluacion::class,
            'inscripcion_id'
        );
    }

    public function asistencias()
    {
        return $this->hasMany(
            Asistencia::class,
            'inscripcion_id'
        );
    }

    public function porcentajeAsistencia(): ?float
    {
        $fila = \DB::table('vista_porcentaje_asistencia')
            ->where('inscripcion_id', $this->id)
            ->first();

        return $fila?->porcentaje_asistencia;
    }
}
