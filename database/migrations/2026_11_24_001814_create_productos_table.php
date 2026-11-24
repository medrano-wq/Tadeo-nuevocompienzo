<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //le decimos quie la tabla tiene una foreign que es categorias y pues categorias id
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->restrictOnDelete();
            $table->string('nombre')->unique();
            $table->text('descripcion');
            $table->decimal('precio', 10, 2);
            $table->unsignedInteger('existencias')->default(0);
            $table->enum('estado', ['Disponible', 'Agotado'])->default('Disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};