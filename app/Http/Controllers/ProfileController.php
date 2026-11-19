<?php
/*aqui basicamente lo que estamos observando es que 
sea el controlador de los perfiles*/
namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
//clase del perfil controlador
class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    //este metod dice que que al recibir la informacion de 
    //request retorna una view y nos dice que retorna a perfil.edit
    //y la funcion lo que hace es empaquetar los datos y mandarlos a la interaccion grafica
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    /*en este metodo tenemos como parametro el profileupdaterequest
    que lo que hace es traer los datos validados y direcciona
    despues tomamos a usuario y lo llenamos con los datos nuevos
    si el usuario 
    ahora el if nos dice si el usuario ingreso un nuevo email
    y despues lo verifica 

    guarda los datos nuevos 
    y return la clase con los datos nuevos
    */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    */
    //este metodo trae el request y direcciona
    //request llama el objeto  para matar al usuario con la funcion para asignar la password que tenga una password y este llenada 
    /*
    despues asiganmos user y eliminamos a usuario invalidamos la session y regeneramos el token
    y te direcciona a la primera pagina
    */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
