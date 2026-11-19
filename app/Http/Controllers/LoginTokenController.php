<?php

namespace App\Http\Controllers;

//aqui nosotros exportamos las clases para poder usarlaas 
//su respectiva ruta
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoginToken;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Mail\LoginTokenMail;

//aqui se declara la clase y es herencia de la clase controller
class LoginTokenController extends Controller
{
    // se declara el metodo mostrar formulario token y retornara una view
    //el cual tiene como tal un retorno y crea una view con la ruta que dice
    public function showTokenForm(): View
    {
        return view('auth.login-token');
    }

    // Envía el token al correo del usuario
    //crea el metodo donde tiene como parametros request que 
// se trae la info del llenado y redirect response es para que
//termine el proceso el request vaida que no este null
//que tenga formato gmail, que exista en la db
//despues busca en la tabla usuario el primer que tenga parentezco
//login token es un metodo estatico que genera el codigo del usuario
    public function sendToken(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

       $user = User::where('email', $request->email)->first();

$loginToken = LoginToken::generateForUser($user);

// Envío en segundo plano mediante colas
//sacamos un destinatario que seria el mail del usuario despues
//le decimos que va estar en queue osea en cola y vamos a instanciar
//un nuevo objeto llamado logintokenmail para sacar el token que ya hicimos

Mail::to($user->email)->queue(new LoginTokenMail($loginToken->token));

//usado para identificar al usuario
//usadp para saber a que usario se va a buscar en la bd
session(['login_token_id' => $loginToken->id]);

//lo dirije a donde tiene que poner el codigo
return redirect()->route('login.code.form');

     
    }

    // Muestra la vista donde se introduce el código de 6 dígitos
    //este metodo es para mostrar el codigo
    //vemos que puede mostrar una view o direccionar una vista
    public function showCodeForm(): View|RedirectResponse
    {
        //dice que si no existe el login token osea
        //intento entrar por la url pues se lo salta y lo 
        //direcciona al login
        if (!session('login_token_id')) {
            return redirect()->route('login');
        }
        //en el caso que exista muestra la vista de autenticacion
        return view('auth.verify-code');
    }

    /*en el metodo primero que todo llamamos como parametro al mensajero
    donde vamos a tener eque direccionar entonces primero validamos 
    que los datos ingresados tengan ciertas caracterisitcas
    despues asignamos session y id del intento de login a token id
    pasamos a las estrucutras selectivas donde si el token id es distinto
    lo que va a hacer es direccionar a login con un error
    si no va a hacer una busqueda con el meodo estatico del eloquent
    para asegurase que el id, el token sean los que buscamos y valida la hora que sea mayo a la que entramos
    y que encuentre el primero
    si es distinto pues retorna con un error
    despues ya logeado lo que hacemos es decirle a auth que ya esta dentro
    del sistema el usuario 
    despues por cuestiones de seguridad borramos el logintokken
    y le asignamos una nueva id 
    ya por ultimo si intenta entrar directo se direcciona al dashboard y 
    para que sea mas facil de leer que diga dashboard y no //8000*/
    public function verifyToken(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string', 'size:6'],
        ]);

        $tokenId = session('login_token_id');

        if (!$tokenId) {
            return redirect()->route('login')->withErrors(['token' => 'La sesión ha expirado. Solicita un nuevo código.']);
        }

        $loginToken = LoginToken::where('id', $tokenId)
            ->where('token', $request->token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$loginToken) {
            return back()->withErrors(['token' => 'El código es inválido o ya ha expirado.']);
        }

        $user = User::find($loginToken->user_id);
        Auth::login($user);

        $loginToken->delete();
        session()->forget('login_token_id');

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}