<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    use HasFactory;
//se puede rellenar el nombre
    protected $fillable = [
        'nombre',
    ];
//un categoria puede tener muchas categorias
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }
}