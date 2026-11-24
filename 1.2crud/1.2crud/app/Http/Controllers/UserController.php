<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    //este metodo es para que los admin puedan ver a los usuarios y los empleados no puedan ver admin
    public function index(Request $request): View
    {
        //query es una funcion que nos trae a los usuario con su rol u es por el ultimo que se creo
        $query = User::with('role')->latest();
 //trash son los usuario eliminados con el soft y solo se pueden mer si eres admin
        $trashedUsers = collect();
        if ($request->user()->isAdmin()) {
            $trashedUsers = User::onlyTrashed()->with('role')->latest('deleted_at')->get();
        }
//el metodo nos da la view de los usuarios y nos trae a los usuarios y a los eliminados
        return view('users.index', [
            'users' => $query->get(),
            'trashedUsers' => $trashedUsers,
        ]);
    }
//estem metodo retorna la informacion y retorna una view
    public function create(Request $request): View
    {
        //aqui lo que nos dice es que si es admin nos traemos todos los roles u si no solo nos traemos el rol de empleado y retorna la view de crear usuario y nos trae los roles
        $roles = $request->user()->isAdmin()
            ? Role::all()
            : Role::where('slug', 'empleado')->get();

        return view('users.create', compact('roles'));
    }
//este metodo es para crear usuarios y el redirect response es para redireccionar a la vista del usuario donde nosotros nos traemos necesarioamente a empleadoroleid porque
//asi asignamos el rol del empleado y su id
    public function store(Request $request): RedirectResponse
    {
        $empleadoRoleId = Role::where('slug', 'empleado')->value('id');
//validamos que esten llenados todos los datos
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['nullable', 'exists:roles,id'],
        ]);
//si el usuario es admin y no esta vacio nos traemos el id u si no lo traemos como empleado
        $roleId = $request->user()->isAdmin() && ! empty($validated['role_id'])
            ? (int) $validated['role_id']
            : $empleadoRoleId;
//se crea el usuario con los datos validados y la contraseña
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $roleId,
        ]);
//redireccionamos a la vista de usuarios y el mensajito
        return redirect()->route('users.index')->with('status', 'Usuario registrado correctamente.');
    }
//este metodo es para mostrar al usuario y nostra la vista del usuario y el rol del usuario
    public function show(User $user): View
    {
        $user->load('role');

        return view('users.show', compact('user'));
    }
//este metodo es para editar al usuario nos retorna la vista del usario ynos trae a los roles 
//si es admin nos trae todos los roles y si no empleado
    public function edit(Request $request, User $user): View
    {
        $roles = $request->user()->isAdmin()
            ? Role::all()
            : Role::where('slug', 'empleado')->get();

        return view('users.edit', compact('user', 'roles'));
    }
//metodo de actualizacion de usuario y validacion
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['nullable', 'exists:roles,id'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }
//si es el admin nos da la posiblidad de cambiar o dejarlo
        if ($request->user()->isAdmin() && ! empty($validated['role_id'])) {
            $data['role_id'] = (int) $validated['role_id'];
        }

        $user->update($data);

        return redirect()->route('users.index')->with('status', 'Usuario actualizado correctamente.');
    }
    //son las rutas del admin

    // Soft Delete admin
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('users.index')->with('status', 'Usuario deshabilitado (Soft Delete).');
    }

    // Restaurar usuario eliminado soft admin
    public function restore(int $id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.index')->with('status', 'Usuario restaurado correctamente.');
    }

    // Eliminación permanente admin
    public function forceDelete(int $id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();

        return redirect()->route('users.index')->with('status', 'Usuario eliminado permanentemente.');
    }
}
