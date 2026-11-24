<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


/* aqui lo que tebenis es la tabla protected porque es la tabla que genera la bd todas las veces que se crea un rol
despues tenemos el metodo de users que tiene muchos osea que un usario puede tener muchos roles 
*/
class Role extends Model
{
    protected $fillable = ['name', 'slug'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}