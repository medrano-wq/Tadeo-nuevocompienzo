<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    // Campos ocultos al serializar a array o JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casteo de atributos
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relación: un usuario pertenece a un rol
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // Relación: perfil de alumno si aplica
    public function alumno(): HasOne
    {
        return $this->hasOne(Alumno::class);
    }

    // Relación: reservaciones del usuario (Práctica 1.5)
    public function reservaciones(): HasMany
    {
        return $this->hasMany(Reservacion::class, 'user_id');
    }

    // Helper práctico para validar rol en controladores o vistas
    public function hasRole(string ...$roles): bool
    {
        $slug = $this->role?->slug;

        return $slug !== null && in_array($slug, $roles, true);
    }
}