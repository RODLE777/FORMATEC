<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sesion extends Model
{
    protected $table = 'sesiones'; 
    public $timestamps = false;
    protected $fillable = ['grupo_id', 'numero_sesion', 'fecha', 'tema', 'hora_inicio', 'hora_fin'];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }
}
