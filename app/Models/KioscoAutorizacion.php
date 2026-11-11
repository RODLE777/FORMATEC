<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class KioscoAutorizacion extends Model
{
    protected $table = 'kiosco_autorizaciones';

    protected $fillable = [
        'usuario_id',
        'estudiante_id',
        'accion',
        'motivo',
        'datos_verificacion',
    ];

    protected function casts(): array
    {
        return [
            'datos_verificacion' => 'array',
        ];
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
