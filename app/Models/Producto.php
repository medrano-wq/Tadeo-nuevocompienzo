<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    use HasFactory;
//facilito pues las tablas que se van a rellenar
    protected $fillable = [
        'categoria_id',
        'nombre',
        'descripcion',
        'precio',
        'existencias',
        'estado',
    ];
// le decimos con el metodo cuales son las conversiones
    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'existencias' => 'integer',
        ];
    }
// le decimos que un producto pues debe pertenecer a una categoria chaval
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }
}