<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = ['inscripcion_id', 'sesion_id', 'asistio', 'observacion', 'registrado_por'];

    protected function casts(): array
    {
        return ['asistio' => 'boolean'];
    }

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function sesion()
    {
        return $this->belongsTo(Sesion::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
