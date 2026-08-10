<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Importacion extends Model
{
    protected $table = 'importaciones';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'archivo_nombre',
        'tipo',
        'total_filas',
        'filas_importadas',
        'filas_error',
        'estado',
        'detalle_errores',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'detalle_errores' => 'array',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}