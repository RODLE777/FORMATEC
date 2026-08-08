<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    // 👇 Agrega esta línea para que use la tabla correcta
    protected $table = 'evaluaciones';

    protected $fillable = [
        'inscripcion_id', 'numero_evaluacion', 'nombre_evaluacion',
        'nota', 'fecha_evaluacion', 'registrado_por',
    ];

    protected function casts(): array
    {
        return [
            'nota' => 'decimal:2',
            'fecha_evaluacion' => 'date',
        ];
    }

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}