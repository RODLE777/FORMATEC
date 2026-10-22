<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';

    protected $fillable = [
        'nombres', 'apellidos', 'sexo', 'fecha_nacimiento', 'dui', 'nit',
        'correo', 'telefono_fijo', 'telefono_celular', 'direccion',
        'comunidad', 'profesion_oficio', 'nivel_estudio', 'enfermedades',
        'usuario_certiport', 'activo', // 'foto' eliminada
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'activo'           => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (Estudiante $estudiante) {
            if (is_null($estudiante->codigo_formatec)) {
                $estudiante->codigo_formatec = 'FTEC-' . str_pad((string) $estudiante->id, 6, '0', STR_PAD_LEFT);
                $estudiante->saveQuietly();
            }
        });
    }

    // Relaciones geográficas eliminadas

    public function encargadoMenor()
    {
        return $this->hasOne(EncargadoMenor::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'inscripciones')
            ->withPivot(['id', 'estado', 'resultado_final', 'nota_final', 'fecha_inscripcion']);
    }

    public function getEdadAttribute(): int
    {
        return Carbon::parse($this->fecha_nacimiento)->age;
    }

    public function getEsMenorEdadAttribute(): bool
    {
        return $this->edad < 18;
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }

    // Método getFotoUrlAttribute() eliminado
}