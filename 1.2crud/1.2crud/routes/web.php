<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
//mostramos la ruta de usuario
Route::get('/', function () {
    return redirect()->route('users.index');
});
//mostramos la ruta para el login que retoorna para validar el login y nos direcciona a la ruta de usuarios
Route::get('/login', function () {
    return view('auth.login');
})->name('login');
//mandamos los datos ingresados para validar el login
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);
//si el login es correcto direccionamos a la ruta y si no da error
  if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended(route('users.index'));
    }
    return back()->withErrors(['email' => 'Credenciales inválidas.'])->onlyInput('email');
});
//mandamos los datos para que se deslogie y invalidamos la sesion  y el token
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');
//rutas para respectivas

Route::middleware(['auth', 'role:admin,empleado'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('/users/{id}/force', [UserController::class, 'forceDelete'])->name('users.force-delete');
});