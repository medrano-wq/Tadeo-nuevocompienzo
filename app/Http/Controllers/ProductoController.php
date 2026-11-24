<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductoController extends Controller
{
    //es para saber que mostrar y que no evita exsponderx xnoxmbrexs internos
    private const ALLOWED_SORTS = [
        'nombre' => 'nombre',
        'precio' => 'precio',
        'existencias' => 'existencias',
        'fecha' => 'created_at',
    ];
//cuantos registros se puede enviar 
    private const ALLOWED_PER_PAGE = [10, 15, 25, 50];
/*
obtiene buscar categoria precios y estado desde el query aplica el trin y por ejemplo el string el int o el float
y valida las diretion que sea asc o desc y compara valores permitdos perpage
*/
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->input('buscar', ''));
        $categoriaId = $request->input('categoria_id');
        $precioMin = $request->input('precio_min');
        $precioMax = $request->input('precio_max');
        $estado = $request->input('estado');

        $sortKey = (string) $request->input('sort', 'fecha');
        $sortColumn = self::ALLOWED_SORTS[$sortKey] ?? 'created_at';

        $directionInput = strtolower((string) $request->input('direction', 'desc'));
        $direction = in_array($directionInput, ['asc', 'desc'], true) ? $directionInput : 'desc';

        $perPageInput = (int) $request->input('per_page', 10);
        $perPage = in_array($perPageInput, self::ALLOWED_PER_PAGE, true) ? $perPageInput : 10;

        $query = Producto::query()
        //carga las categorias relacionadas
            ->with('categoria')
            //filtrapor coincidencia si se escribio el termino
            ->when($buscar !== '', function ($q) use ($buscar) {
                $q->where('nombre', 'like', '%'.$buscar.'%');
            })
            //a;ade clausulas para que este presente o valido
            ->when(filled($categoriaId), function ($q) use ($categoriaId) {
                $q->where('categoria_id', (int) $categoriaId);
            })
            //filtra productos por precio mayor o igual al minimo
            ->when($precioMin !== null && $precioMin !== '', function ($q) use ($precioMin) {
                $q->where('precio', '>=', (float) $precioMin);
            })
            //filtra productos por menor o igual hasta el maximo
            ->when($precioMax !== null && $precioMax !== '', function ($q) use ($precioMax) {
                $q->where('precio', '<=', (float) $precioMax);
            })
//por estados solo si aplica los valores
            ->when(in_array($estado, ['Disponible', 'Agotado'], true), function ($q) use ($estado) {
                $q->where('estado', $estado);
            })
            //aplica el orden elegido
            ->orderBy($sortColumn, $direction);
//guarda los paramaetros de filtro
        $productos = $query->paginate($perPage)->withQueryString();
        //obtiene todas las categorias ordenadas por alfabeto
        $categorias = Categoria::orderBy('nombre')->get();
//verifica si el rol del usuario tiene permisos para gestionar los productos
        $userRole = $request->user()?->role?->slug;
        $puedeGestionar = in_array($userRole, ['admin', 'administrador', 'profesor'], true);
//muestra la vista produyctos pasando los datos y los estados actuales de los filtros
        return view('productos.index', [
            'productos' => $productos,
            'categorias' => $categorias,
            'sort' => $sortKey,
            'direction' => $direction,
            'perPage' => $perPage,
            'perPageOptions' => self::ALLOWED_PER_PAGE,
            'puedeGestionar' => $puedeGestionar,
            'filtros' => [
                'buscar' => $buscar,
                'categoria_id' => $categoriaId,
                'precio_min' => $precioMin,
                'precio_max' => $precioMax,
                'estado' => $estado,
            ],
        ]);
    }
//muestra el formulario para crear uno nuevo solo usuarios autorizados
    public function create(Request $request): View
    {
        //bloquea el acceso si el usuario no tiene el rol
        $this->autorizarGestion($request);
//retorna a la vista del formulario
        return view('productos.create', [
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }
//valida y guarda un nuevo producto en la bd
    public function store(ProductoRequest $request): RedirectResponse
    {
        //verifica permisos antes de guardar
        $this->autorizarGestion($request);
//crea el registro usando los datos validados
        Producto::create($request->validated());
//mensaje de redireccion que ya todo chidpo
        return redirect()
            ->route('productos.index')
            ->with('status', 'Producto registrado correctamente.');
    }
//valida y actualiza los datos de un producto existente
    public function edit(Request $request, Producto $producto): View
    {
        //verifica
        $this->autorizarGestion($request);
//retorna vamos mas rapido porque pues ya es muy repetitivo 
//sabesmos que producto depende de categoria
        return view('productos.edit', [
            'producto' => $producto,
            'categorias' => Categoria::orderBy('nombre')->get(),
        ]);
    }
//acutaliza puedes los datos
    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        //verifica los permisos antes de acutalizar
        $this->autorizarGestion($request);
//actualiza con los datos validados
        $producto->update($request->validated());
//retorna que fue bien actualizados
        return redirect()
            ->route('productos.index')
            ->with('status', 'Producto actualizado correctamente.');
    }
//lo matamos
    public function destroy(Request $request, Producto $producto): RedirectResponse
    {
    //verifica por 4 vez que este validado
        $this->autorizarGestion($request);
// borra los registros
        $producto->delete();
//redirige que se elimino correctamente
        return redirect()
            ->route('productos.index')
            ->with('status', 'Producto eliminado correctamente.');
    }
//comprueba que tenga el rol si no 403 error papu
    private function autorizarGestion(Request $request): void
    {
        $role = $request->user()?->role?->slug;

        if (! in_array($role, ['admin', 'administrador', 'profesor'], true)) {
            abort(403, 'No tienes permisos para administrar productos.');
        }
    }
}