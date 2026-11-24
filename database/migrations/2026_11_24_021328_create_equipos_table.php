<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //;a tablita equipos pues que tiene esas shit para las otras shits u pues yua
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->string('ubicacion')->nullable();
            $table->text('especificaciones')->nullable();
            $table->enum('estado', ['Disponible', 'Reservado', 'Mantenimiento'])->default('Disponible');
            $table->timestamps();
        });
    }

    //esta down
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};