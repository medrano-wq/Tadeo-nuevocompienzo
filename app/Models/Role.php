<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;
//las tablas que podemos rellenar
    protected $fillable = [
        'name',
        'slug',
    ];
//decimos que un usuario puede tener una relacion a muchos usuariois
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}