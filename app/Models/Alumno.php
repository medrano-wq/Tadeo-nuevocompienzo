<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alumno extends Model
{
    use HasFactory;
// que se puede rellenar la tablas p
    protected $fillable = [
        'user_id',
        'nombre',
        'faltas',
        'calificacion',
    ];
// es para poder transcribit lo que se escribe asi en el formato que es
    protected function casts(): array
    {
        return [
            'faltas' => 'integer',
            'calificacion' => 'float',
        ];
    }
//es te metodo dice que el usuario pertenece a una un user_id
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
//a aqui lo que hace es retornar el atributo y obtener la calificacion dependiendo de la nota
//obtenemos un get solo para lectura y por eso es string despues nota para que siempre sea float y que obtengamos la calificacion
//ya solo retornamos el atributo creado
    protected function equivalencia(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $nota = (float) $this->calificacion;

                // Ejemplo en escala 0-100 (ajusta si usas 0-10):
                // NA: < 70 | SA: 70 - 79 | DE: 80 - 89 | AU: >= 90
                if ($nota < 70) {
                    return 'NA';
                }

                if ($nota < 80) {
                    return 'SA';
                }

                if ($nota < 90) {
                    return 'DE';
                }

                return 'AU';
            }
        );
    }
}