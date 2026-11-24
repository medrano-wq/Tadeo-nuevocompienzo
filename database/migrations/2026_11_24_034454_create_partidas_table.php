<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //generamos las respectiva tabla donde almacenaremos los datos y usamos dos foreing una para cada jugador
        // que tendra pues su informacion requerida
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_x_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('jugador_o_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('tablero');
            $table->enum('turno_simbolo', ['X', 'O'])->default('X');
            $table->foreignId('turno_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('estado', [
                'esperando',
                'en_curso',
                'gana_x',
                'gana_o',
                'empate',
                'abandonada',
                'cancelada',
            ])->default('esperando');
            $table->foreignId('ganador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('abandonada_por_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};