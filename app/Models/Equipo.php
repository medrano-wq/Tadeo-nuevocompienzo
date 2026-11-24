<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipo extends Model
{
    protected $table = 'equipos';
//ok tabla que se puede rellenar
    protected $fillable = [
        'codigo',
        'nombre',
        'ubicacion',
        'especificaciones',
        'estado',
    ];
//metodo para hacer reservaciones sabemos que un equipo puede tener muchas reservacioines
    public function reservaciones(): HasMany
    {
        return $this->hasMany(Reservacion::class, 'equipo_id');
    }
//cuando reservacion esta activa pues solo puede una pues si kakaka estoy callendo en locurajejejeje
    public function reservacionActiva()
    {
        return $this->hasOne(Reservacion::class, 'equipo_id')
            ->where('estado', 'Activa')
            ->latestOfMany();
    }
}