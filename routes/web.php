<?php

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReservacionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PartidaController;


Route::get('/', fn () => redirect()->route('alumnos.index'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    // --- Práctica 1.3: Alumnos ---
    Route::get('/alumnos', [AlumnoController::class, 'index'])->name('alumnos.index');

    Route::middleware('role:profesor')->group(function () {
        Route::get('/alumnos/create', [AlumnoController::class, 'create'])->name('alumnos.create');
        Route::post('/alumnos', [AlumnoController::class, 'store'])->name('alumnos.store');
        Route::get('/alumnos/{alumno}/edit', [AlumnoController::class, 'edit'])->name('alumnos.edit');
        Route::put('/alumnos/{alumno}', [AlumnoController::class, 'update'])->name('alumnos.update');
        Route::delete('/alumnos/{alumno}', [AlumnoController::class, 'destroy'])->name('alumnos.destroy');
    });

    // --- Práctica 1.4: Productos ---
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');

    Route::middleware('role:admin,administrador,profesor')->group(function () {
        Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    });

    // --- Práctica 1.5: Equipos y Reservaciones ---
    Route::get('/equipos', [EquipoController::class, 'index'])->name('equipos.index');

    Route::middleware('role:admin,administrador')->group(function () {
        Route::get('/equipos/create', [EquipoController::class, 'create'])->name('equipos.create');
        Route::post('/equipos', [EquipoController::class, 'store'])->name('equipos.store');
        Route::get('/equipos/{equipo}/edit', [EquipoController::class, 'edit'])->name('equipos.edit');
        Route::put('/equipos/{equipo}', [EquipoController::class, 'update'])->name('equipos.update');
        Route::delete('/equipos/{equipo}', [EquipoController::class, 'destroy'])->name('equipos.destroy');
    });

    Route::get('/reservaciones', [ReservacionController::class, 'index'])->name('reservaciones.index');
    Route::get('/reservaciones/{reservacion}', [ReservacionController::class, 'show'])->name('reservaciones.show');
    Route::post('/reservaciones', [ReservacionController::class, 'store'])->name('reservaciones.store');
    Route::patch('/reservaciones/{reservacion}/cancelar', [ReservacionController::class, 'cancel'])->name('reservaciones.cancel');

    // --- Práctica 1.6: Juego del Gato (Multijugador) ---
    Route::get('/gato', [PartidaController::class, 'index'])->name('gato.index');
    Route::post('/gato', [PartidaController::class, 'store'])->name('gato.store');
    Route::get('/gato/{partida}', [PartidaController::class, 'show'])->name('gato.show');
    Route::post('/gato/{partida}/unirse', [PartidaController::class, 'unirse'])->name('gato.unirse');
    Route::post('/gato/{partida}/jugar', [PartidaController::class, 'jugar'])->name('gato.jugar');
    Route::post('/gato/{partida}/abandonar', [PartidaController::class, 'abandonar'])->name('gato.abandonar');
    Route::post('/gato/{partida}/cancelar', [PartidaController::class, 'cancelar'])->name('gato.cancelar');
    Route::get('/gato/{partida}/estado', [PartidaController::class, 'estadoJson'])->name('gato.estado');
    });