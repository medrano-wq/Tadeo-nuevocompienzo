<?php

namespace App\Http\Controllers;
use App\Models\Alumno;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlumnoController extends Controller
{
    public function index(Request $request): View
    {
        //nos traemos al usuario
        $user = $request->user();

        /*
        esta condicional nos dice que podemos tener usuarios y aceptar que tengan valores nulos y despues nos dice 
        que si es igual al valor asignado alumno va a sacar el eloquent para buscar en la bd los alumnos y mostrarlos
        del primero al ultimo
        despues retorna el alumnos index
        donde tenemos es alumno para ocultar botones del admin
        despues tenemos a alumno para podder asignar los datos del alumno
        alumnos para usarlo en un foreach para evitar falta de valores
        despues el sort y direccion son para valores predefinidos 
        y son para las tablas
        */
        if ($user?->role?->slug === 'alumno') {
            $alumno = Alumno::where('user_id', $user->id)->first();

            return view('alumnos.index', [
                'esAlumno' => true,
                'alumno' => $alumno,
                'alumnos' => collect(),
                'sort' => 'nombre',
                'direction' => 'asc',
            ]);
        }
        //validacion para las tablas que se pueden ordenar la lista de alumnos

        $allowedSorts = ['nombre', 'calificacion', 'faltas'];
        //permitir solo el abc 
        $allowedDirections = ['asc', 'desc'];
// aqui lo que le estamos diciendo es que como condicion el arreglo lo ordene con las condiciones antes dichas
//para mostrar de forma ordenada a los alumnos con sus respectivas ambitos
//y puede ser nulll y si no pasa la validacion asigna nomnre
        $sort = in_array($request->query('sort'), $allowedSorts, true)
            ? $request->query('sort')
            : 'nombre';
//aqui direccion lee el valor del parametro direcccion despues comprueba que exisste edn xelx xarrexglo y despues pasa si la vali
//cion es correcta y se asigna el valor en direction y si no pues se asigna asc
        $direction = in_array($request->query('direction'), $allowedDirections, true)
            ? $request->query('direction')
            : 'asc';
//consulta de alumnas ordenadas por califciaciones 
        $alumnos = Alumno::query()
            ->orderBy($sort, $direction)
            ->get();
//retornamos a los alumnos para mostrar si es docente 
        return view('alumnos.index', [
            'esAlumno' => false,
            'alumno' => null,
            'alumnos' => $alumnos,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }
//retornamos que sea alumnos crear
    public function create(): View
    {
        return view('alumnos.create');
    }
//mostramos los campos a llenar del alumno
//con sus requermientos y retornamos si se crea al alumno
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'faltas' => ['required', 'integer', 'min:0', 'max:200'],
            'calificacion' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        Alumno::create($validated);
//si se comprueba que esta validado se retorna a alumno y el mensjae
        return redirect()
            ->route('alumnos.index')
            ->with('status', 'Alumno registrado correctamente.');
    }
//redireccionamos y creamos el arreglo alumno
    public function edit(Alumno $alumno): View
    {
        return view('alumnos.edit', compact('alumno'));
    }
//su respectivo metodo para actualizar  el alumno y pues los datos que tiene que asignar 
//la condicion de validado y pues la ruta donde se redirecciona
    public function update(Request $request, Alumno $alumno): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'faltas' => ['required', 'integer', 'min:0', 'max:200'],
            'calificacion' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $alumno->update($validated);

        return redirect()
            ->route('alumnos.index')
            ->with('status', 'Registro académico actualizado.');
    }
//cuando matamos a un alumno y pues nada mas
    public function destroy(Alumno $alumno): RedirectResponse
    {
        $alumno->delete();

        return redirect()
            ->route('alumnos.index')
            ->with('status', 'Registro eliminado.');
    }
}