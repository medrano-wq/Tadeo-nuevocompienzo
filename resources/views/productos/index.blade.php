@extends('layouts.app')

@section('title', 'Gestión de productos')

@section('content')
    @php
        $nextDir = fn (string $col) => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
        $indicator = fn (string $col) => $sort === $col ? ($direction === 'asc' ? ' ↑' : ' ↓') : '';
        $sortUrl = fn (string $col) => request()->fullUrlWithQuery([
            'sort' => $col,
            'direction' => $nextDir($col),
            'page' => 1,
        ]);
    @endphp

    <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
        <h1 style="margin:0;">Catálogo de productos</h1>
        @if ($puedeGestionar)
            <a href="{{ route('productos.create') }}" class="btn btn-primary">+ Nuevo producto</a>
        @endif
    </div>

    <form action="{{ route('productos.index') }}" method="GET" style="margin-top:1rem;padding:1rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;">
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="direction" value="{{ $direction }}">

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:0.75rem;">
            <div>
                <label for="buscar" style="display:block;font-weight:600;margin-bottom:0.25rem;">Buscar por nombre</label>
                <input id="buscar" type="text" name="buscar" value="{{ $filtros['buscar'] }}" placeholder="Ej. Teclado" style="width:100%;padding:0.45rem;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;">
            </div>

            <div>
                <label for="categoria_id" style="display:block;font-weight:600;margin-bottom:0.25rem;">Categoría</label>
                <select id="categoria_id" name="categoria_id" style="width:100%;padding:0.45rem;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;">
                    <option value="">Todas</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}" @selected((string) $filtros['categoria_id'] === (string) $categoria->id)>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="precio_min" style="display:block;font-weight:600;margin-bottom:0.25rem;">Precio mínimo</label>
                <input id="precio_min" type="number" step="0.01" min="0" name="precio_min" value="{{ $filtros['precio_min'] }}" style="width:100%;padding:0.45rem;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;">
            </div>

            <div>
                <label for="precio_max" style="display:block;font-weight:600;margin-bottom:0.25rem;">Precio máximo</label>
                <input id="precio_max" type="number" step="0.01" min="0" name="precio_max" value="{{ $filtros['precio_max'] }}" style="width:100%;padding:0.45rem;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;">
            </div>

            <div>
                <label for="estado" style="display:block;font-weight:600;margin-bottom:0.25rem;">Estado</label>
                <select id="estado" name="estado" style="width:100%;padding:0.45rem;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;">
                    <option value="">Todos</option>
                    <option value="Disponible" @selected($filtros['estado'] === 'Disponible')>Disponible</option>
                    <option value="Agotado" @selected($filtros['estado'] === 'Agotado')>Agotado</option>
                </select>
            </div>

            <div>
                <label for="per_page" style="display:block;font-weight:600;margin-bottom:0.25rem;">Por página</label>
                <select id="per_page" name="per_page" style="width:100%;padding:0.45rem;border:1px solid #cbd5e1;border-radius:6px;box-sizing:border-box;">
                    @foreach ($perPageOptions as $size)
                        <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-top:0.85rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">Aplicar filtros</button>
            <a href="{{ route('productos.index') }}" class="btn">Limpiar filtros</a>
        </div>
    </form>

    <div style="margin-top:1rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.75rem;">
        <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
            <a href="{{ $sortUrl('nombre') }}" class="btn">Nombre{{ $indicator('nombre') }}</a>
            <a href="{{ $sortUrl('precio') }}" class="btn">Precio{{ $indicator('precio') }}</a>
            <a href="{{ $sortUrl('existencias') }}" class="btn">Existencias{{ $indicator('existencias') }}</a>
            <a href="{{ $sortUrl('fecha') }}" class="btn">Fecha de registro{{ $indicator('fecha') }}</a>
        </div>

        <div style="font-size:0.9rem;color:#374151;">
            Total de registros: <strong>{{ $productos->total() }}</strong> |
            Página <strong>{{ $productos->currentPage() }}</strong> de <strong>{{ max(1, $productos->lastPage()) }}</strong>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Existencias</th>
                <th>Estado</th>
                <th>Fecha de registro</th>
                @if ($puedeGestionar)
                    <th>Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($productos as $producto)
                <tr>
                    <td>{{ $producto->id }}</td>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->descripcion }}</td>
                    <td>{{ $producto->categoria?->nombre ?? 'Sin categoría' }}</td>
                    <td>${{ number_format((float) $producto->precio, 2) }}</td>
                    <td>{{ $producto->existencias }}</td>
                    <td><x-estado-producto-badge :estado="$producto->estado" /></td>
                    <td>{{ $producto->created_at?->format('Y-m-d H:i') }}</td>
                    @if ($puedeGestionar)
                        <td style="display:flex;gap:0.4rem;">
                            <a href="{{ route('productos.edit', $producto) }}" class="btn">Editar</a>
                            <form action="{{ route('productos.destroy', $producto) }}" method="POST" style="margin:0;" onsubmit="return confirm('¿Eliminar este producto?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $puedeGestionar ? 9 : 8 }}">No se encontraron productos con los criterios seleccionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:1rem;">
        {{ $productos->links() }}
    </div>
@endsection