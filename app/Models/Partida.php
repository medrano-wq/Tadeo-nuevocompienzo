<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Partida extends Model
{
    use HasFactory;
//aqui nos traemos los datos a rellenar 
    protected $fillable = [
        'jugador_x_id',
        'jugador_o_id',
        'tablero',
        'turno_simbolo',
        'turno_user_id',
        'estado',
        'ganador_id',
        'abandonada_por_id',
    ];
//pues aqui solo que se alinien al array 
    protected $casts = [
        'tablero' => 'array',
    ];
    //sabemos que los jugadores x pues tiene una partida asi con la o pues con todos realmente porque todos tiene un turno ganan o pierden por reglas del juego

    public function jugadorX(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jugador_x_id');
    }

    public function jugadorO(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jugador_o_id');
    }

    public function turnoUsuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'turno_user_id');
    }

    public function ganador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ganador_id');
    }

    public function abandonadaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'abandonada_por_id');
    }
}