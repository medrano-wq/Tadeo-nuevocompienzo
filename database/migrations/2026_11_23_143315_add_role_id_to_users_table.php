<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /*
    En esta funcion le decimos que los usuarios
    tiene una relacion con la tabla roles y usamos
    como foreign key el role_id
    y usamos el after se usa despues de la contraseña
    y pues le decimos que es de roles y que no se puede
    eliminar si un usuario tiene rol
    */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId("role_id")
            ->after("password")
            ->constrained("roles")
            ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    //si necesitamos eliminar eliminamos la columna

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropConstrainedForeignId("role_id");
        });
    }
};
