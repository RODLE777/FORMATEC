<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncargadoMenor extends Model
{
    protected $table = 'encargados_menor';  // 👈 Especifica el nombre correcto

    protected $fillable = [
        'estudiante_id',
        'nombre_completo',
        'parentesco',
        'telefono',
    ];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }
}