<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Importacion extends Model
{

protected $table = 'importaciones';

    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'usuario_id', 'archivo_nombre', 'tipo', 'total_filas',
        'filas_importadas', 'filas_error', 'estado', 'detalle_errores',
    ];

    protected function casts(): array
    {
        return ['detalle_errores' => 'array'];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
