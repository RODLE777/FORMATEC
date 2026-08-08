<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteGenerado extends Model
{
    protected $table = 'reportes_generados';
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = ['usuario_id', 'tipo_reporte', 'parametros', 'archivo'];

    protected function casts(): array
    {
        return ['parametros' => 'array'];
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
