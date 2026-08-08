<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Estudiante extends Model
{
    protected $table = 'estudiantes';  // 👈 Cambiado de 'encargados_menor' a 'estudiantes'
    
    protected $fillable = [
        'nombres', 'apellidos', 'sexo', 'fecha_nacimiento', 'dui', 'nit',
        'correo', 'telefono_fijo', 'telefono_celular', 'direccion',
        'departamento_id', 'municipio_id', 'distrito_id', 'comunidad',
        'profesion_oficio', 'nivel_estudio', 'enfermedades',
        'usuario_certiport', 'activo',
    ];

    // codigo_formatec NUNCA se asigna desde el formulario: lo genera el
    // trigger trg_estudiante_codigo al insertar (ver seccion 4.2 del
    // prompt maestro). Se deja fuera de $fillable a proposito.
    protected $guarded = ['id', 'codigo_formatec'];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'activo' => 'boolean',
        ];
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

    /**
     * Edad calculada al vuelo. MySQL no permite CURDATE() en columnas
     * generadas, por eso se resuelve aqui (o via vista_estudiantes) y
     * nunca como columna fisica. Ver seccion 4.2 del prompt maestro.
     */
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
}