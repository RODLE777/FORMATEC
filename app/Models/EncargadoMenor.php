<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EncargadoMenor extends Model
{
    public $timestamps = false;
    protected $table = 'encargados_menor';  

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