@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')
    <h1>Editar producto</h1>

    <form action="{{ route('productos.update', $producto) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="field">
            <label for="nombre">Nombre</label>
            <input id="nombre" type="text" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
        </div>

        <div class="field">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="3" required style="width:100%;max-width:420px;padding:0.5rem;border:1px solid #cbd5e1;border-radius:6px;">{{ old('descripcion', $producto->descripcion) }}</textarea>
        </div>

        <div class="field">
            <label for="categoria_id">Categoría</label>
            <select id="categoria_id" name="categoria_id" required style="width:100%;max-width:420px;padding:0.5rem;border:1px solid #cbd5e1;border-radius:6px;">
                <option value="">Selecciona una categoría</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" @selected((string) old('categoria_id', $producto->categoria_id) === (string) $categoria->id)>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="precio">Precio</label>
            <input id="precio" type="number" step="0.01" min="0.01" name="precio" value="{{ old('precio', $producto->precio) }}" required>
        </div>

        <div class="field">
            <label for="existencias">Existencias</label>
            <input id="existencias" type="number" min="0" name="existencias" value="{{ old('existencias', $producto->existencias) }}" required>
        </div>

        <div class="field">
            <label for="estado">Estado</label>
            <select id="estado" name="estado" required style="width:100%;max-width:420px;padding:0.5rem;border:1px solid #cbd5e1;border-radius:6px;">
                <option value="Disponible" @selected(old('estado', $producto->estado) === 'Disponible')>Disponible</option>
                <option value="Agotado" @selected(old('estado', $producto->estado) === 'Agotado')>Agotado</option>
            </select>
        </div>

        <div style="display:flex;gap:0.5rem;">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="{{ route('productos.index') }}" class="btn">Cancelar</a>
        </div>
    </form>
@endsection