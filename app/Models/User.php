<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'rol', 'activo',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function profesor()
    {
        return $this->hasOne(Profesor::class);
    }

    public function esRoot(): bool
    {
        return $this->rol === 'ROOT';
    }

    public function esAdministrador(): bool
    {
        return $this->rol === 'ADMINISTRADOR';
    }

    public function esProfesor(): bool
    {
        return $this->rol === 'PROFESOR';
    }

    /** ROOT y ADMINISTRADOR comparten la gestion operativa diaria. */
    public function puedeGestionarOperacion(): bool
    {
        return in_array($this->rol, ['ROOT', 'ADMINISTRADOR'], true);
    }
}
