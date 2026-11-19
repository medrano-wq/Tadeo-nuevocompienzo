<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoginToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. Valida las credenciales ingresadas (correo y contraseña)
        $request->authenticate();

        $user = $request->user();

        // 2. Si el usuario es ADMINISTRADOR, interceptamos el login
        if ($user->role === 'admin') {
            // Generamos el token de verificación en la base de datos
            $loginToken = LoginToken::generateForUser($user);

            // Enviamos el correo con los 6 dígitos a través de SMTP (Gmail)
            Mail::raw("Tu código de verificación de administrador es: {$loginToken->token}", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Código de Verificación 2FA - Administrador');
            });

            // Guardamos el ID del token en la sesión para validarlo después
            session(['login_token_id' => $loginToken->id]);

            // Deslogueamos temporalmente al usuario para que no entre al dashboard directamente
            Auth::guard('web')->logout();

            // Redirigimos al formulario para ingresar el código
            return redirect()->route('login.code.form');
        }

        // 3. Si es EMPLEADO, entra directo y regenera la sesión
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}