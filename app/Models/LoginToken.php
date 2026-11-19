<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
    ];

    /**
     * Genera un token aleatorio de 6 dígitos para el usuario dado.
     */

    
    public static function generateForUser(User $user): self
    {
        // Opcional: puedes eliminar tokens anteriores de este usuario si deseas que solo sirva el último
        self::where('user_id', $user->id)->delete();

        return self::create([
            'user_id' => $user->id,
            'token'   => rand(100000, 999999), // Código aleatorio de 6 dígitos
            'expires_at' => now()->addMinutes(10), // Expira en 10 minutos
        ]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}