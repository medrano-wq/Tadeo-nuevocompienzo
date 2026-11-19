<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginTokenController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth', "admin.2fa")->group(function () {
    Route::get("/dashboard", [DashboardController::class, "index"])->name("dashboard");
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// --- RUTAS LIMPIAS PARA EL 2FA DEL ADMINISTRADOR ---
Route::middleware('guest')->group(function () {
    Route::get('login/verify-code', [LoginTokenController::class, 'showCodeForm'])->name('login.code.form');
    Route::post('login/verify-code', [LoginTokenController::class, 'verifyToken'])->name('login.code.verify');
});

