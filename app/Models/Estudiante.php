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
        'departamento_id', 'municipio_id', 'distrito_id', 'comunidad',
        'profesion_oficio', 'nivel_estudio', 'enfermedades',
        'usuario_certiport', 'foto', 'activo',
    ];

    // codigo_formatec NUNCA se asigna desde el formulario: lo genera
    // automáticamente el evento created() de abajo.
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

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }

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

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? \Storage::disk('public')->url($this->foto) : null;
    }
}