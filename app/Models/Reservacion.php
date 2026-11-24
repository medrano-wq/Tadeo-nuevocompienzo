<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservacion extends Model
{
    //datos que estan reservaciones
    protected $table = 'reservaciones';
//se llenan los datos pues necesarios
    protected $fillable = [
        'user_id',
        'equipo_id',
        'fecha_reserva',
        'estado',
    ];
//los datos que pueden ser convertidos son muchiosmos guiauauauau
    protected function casts(): array
    {
        return [
            'fecha_reserva' => 'datetime',
        ];
    }
//sabemos que pues un usario tiene una reservacion caray
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
//el equipo tambien necesita una reservacion y se complementa con lo que ya habiamos dicho
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }
}