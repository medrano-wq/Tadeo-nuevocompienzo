@extends('layouts.app')

@section('title', 'Editar alumno')

@section('content')
    <h1>Editar alumno</h1>

    <form action="{{ route('alumnos.update', $alumno) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="field">
            <label for="nombre">Nombre del alumno</label>
            <input id="nombre" type="text" name="nombre" value="{{ old('nombre', $alumno->nombre) }}" required>
        </div>

        <div class="field">
            <label for="faltas">Número de faltas</label>
            <input id="faltas" type="number" name="faltas" value="{{ old('faltas', $alumno->faltas) }}" min="0" required>
        </div>

        <div class="field">
            <label for="calificacion">Calificación numérica</label>
            <input id="calificacion" type="number" step="0.01" name="calificacion" value="{{ old('calificacion', $alumno->calificacion) }}" min="0" max="100" required>
        </div>

        <div style="display:flex;gap:0.5rem;">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="{{ route('alumnos.index') }}" class="btn">Cancelar</a>
        </div>
    </form>
@endsection