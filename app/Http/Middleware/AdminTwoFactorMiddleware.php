<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
/*
la clase admite los dos factores de middleware
despues el closure dice que puede ser algo que se puede hacer despues
el next direcciona al siguiente osea hace lo mismo que view
si valida que exista lo que hace es guardar y redirecciona al login codeform
*/
class AdminTwoFactorMiddleware
{
    /*
      
    */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            if (session()->has('login_token_id')) {
                Auth::guard('web')->logout();
                return redirect()->route('login.code.form');
            }
        }

        return $next($request);
    }
}