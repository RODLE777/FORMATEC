<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
     protected $table = 'profesores';   
    protected $fillable = [
        'user_id', 'nombres', 'apellidos', 'dui', 'telefono',
        'correo', 'especialidad', 'activo',
    ];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }
}
