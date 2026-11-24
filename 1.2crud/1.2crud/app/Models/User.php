<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    //la razon de usar hasfactory es para crear usuarios prueba, notifiable para notis y soft para el borrado
    use HasFactory, Notifiable, SoftDeletes;



    //creamos una tabla que se puede llenar con los datos basicos
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];
//usamos el hidden para que no se pueda ver la contraseña y el token
    protected $hidden = [
        'password',
        'remember_token',
    ];
//casts para ver la fecha de verificiacion y la contrasela se encripta y en array para la bd
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
 //el metodo role que que se asigne a cada usuario
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
//si el usuario tiene un rol en especifico
    public function hasRole(string $slug): bool
    {
        return $this->role?->slug === $slug;
    }
//si el chaval es el putisimo admin
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}