<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    // el metodo pues la lista puedes de los equipos 
    public function index(Request $request)
    {
        //asigna los valores del usuario
        $user = $request->user();
        //si es admin el chaval
        $esAdmin = in_array($user->role?->slug, ['admin', 'administrador'], true);
//hacemos la consulta ordenada por el equipo
        $query = Equipo::query()->orderBy('codigo');
//si no es admin pues solo muestra el equipo disponible
        if (! $esAdmin) {
            $query->where('estado', 'Disponible');
            //si si es admin se envia el fitro por estado y filtra por valor
        } elseif ($request->filled('estado')) {
            $query->where('estado', $request->string('estado'));
        }
//para conservarlos parametros de la pagina
        $equipos = $query->paginate(10)->withQueryString();
//retorna equipos y envia admin y equipos
        return view('equipos.index', compact('equipos', 'esAdmin'));
    }
//creamos
    public function create()
    {
        return view('equipos.create');
    }
//validamos y creamos el nuevo equipo
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:equipos,codigo'],
            'nombre' => ['required', 'string', 'max:120'],
            'ubicacion' => ['nullable', 'string', 'max:120'],
            'especificaciones' => ['nullable', 'string', 'max:500'],
            'estado' => ['required', 'in:Disponible,Reservado,Mantenimiento'],
        ]);

        Equipo::create($validated);

        return redirect()->route('equipos.index')
            ->with('status', 'Equipo registrado correctamente.');
    }
//editamos y validamos el equipo 
    public function edit(Equipo $equipo)
    {
        return view('equipos.edit', compact('equipo'));
    }

    public function update(Request $request, Equipo $equipo)
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:equipos,codigo,' . $equipo->id],
            'nombre' => ['required', 'string', 'max:120'],
            'ubicacion' => ['nullable', 'string', 'max:120'],
            'especificaciones' => ['nullable', 'string', 'max:500'],
            'estado' => ['required', 'in:Disponible,Reservado,Mantenimiento'],
        ]);

        $equipo->update($validated);

        return redirect()->route('equipos.index')
            ->with('status', 'Equipo actualizado correctamente.');
    }
//matamos al equipo 
    public function destroy(Equipo $equipo)
    {
        if ($equipo->reservaciones()->where('estado', 'Activa')->exists()) {
            return back()->withErrors([
                'equipo' => 'No se puede eliminar un equipo con una reservación activa.',
            ]);
        }

        $equipo->delete();

        return redirect()->route('equipos.index')
            ->with('status', 'Equipo eliminado correctamente.');
    }
}