<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class AuthController extends Controller
{
    /*
    showLogin retorna la vista para iniciar sesión.
    */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /*
    Valida las credenciales; si Auth::attempt es exitoso, regenera la sesión
    y redirige a alumnos.index. Si falla, regresa con un mensaje de error.
    */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('alumnos.index'));
        }

        return back()->withErrors([
            'email' => 'Credenciales inválidas.',
        ])->onlyInput('email');
    }

    /*
    Cierra la sesión, invalida la sesión actual, regenera el token CSRF
    y redirige a la vista de login.
    */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}