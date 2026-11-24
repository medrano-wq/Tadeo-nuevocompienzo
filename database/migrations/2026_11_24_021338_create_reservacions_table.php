<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    // las mismas tablitas pues ya sabemos y esas cosa bien importantes pues aja para el aja u aja
        Schema::create('reservaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('equipo_id')->constrained('equipos')->restrictOnDelete();
            $table->timestamp('fecha_reserva');
            $table->enum('estado', ['Activa', 'Cancelada', 'Finalizada'])->default('Activa');
            $table->timestamps();
        });
    }
//ta trite
    public function down(): void
    {
        Schema::dropIfExists('reservaciones');
    }
};