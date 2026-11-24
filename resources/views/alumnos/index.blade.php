@extends('layouts.app')

@section('title', 'Información académica')

@section('content')
    @if ($esAlumno)
        <h1>Mi información académica</h1>

        @if ($alumno)
            <table>
                <thead>
                    <tr>
                        <th>Nombre del alumno</th>
                        <th>Número de faltas</th>
                        <th>Calificación</th>
                        <th>Equivalencia</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $alumno->nombre }}</td>
                        <td>{{ $alumno->faltas }}</td>
                        <td>{{ $alumno->calificacion }}</td>
                        <td><x-equivalencia-badge :equivalencia="$alumno->equivalencia" /></td>
                    </tr>
                </tbody>
            </table>
        @else
            <p>Aún no tienes un registro académico vinculado a tu cuenta.</p>
        @endif
    @else
        @php
            $nextDir = fn (string $col) => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc';
            $indicator = fn (string $col) => $sort === $col ? ($direction === 'asc' ? ' ↑' : ' ↓') : '';
        @endphp

        <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
            <h1 style="margin:0;">Listado de alumnos</h1>
            <a href="{{ route('alumnos.create') }}" class="btn btn-primary">+ Registrar alumno</a>
        </div>

        <div style="margin-top:1rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
            <a class="btn" href="{{ route('alumnos.index', ['sort' => 'nombre', 'direction' => $nextDir('nombre')]) }}">
                Ordenar por nombre{{ $indicator('nombre') }}
            </a>
            <a class="btn" href="{{ route('alumnos.index', ['sort' => 'calificacion', 'direction' => $nextDir('calificacion')]) }}">
                Ordenar por calificación{{ $indicator('calificacion') }}
            </a>
            <a class="btn" href="{{ route('alumnos.index', ['sort' => 'faltas', 'direction' => $nextDir('faltas')]) }}">
                Ordenar por faltas{{ $indicator('faltas') }}
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nombre del alumno</th>
                    <th>Número de faltas</th>
                    <th>Calificación</th>
                    <th>Equivalencia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($alumnos as $item)
                    <tr>
                        <td>{{ $item->nombre }}</td>
                        <td>{{ $item->faltas }}</td>
                        <td>{{ $item->calificacion }}</td>
                        <td><x-equivalencia-badge :equivalencia="$item->equivalencia" /></td>
                        <td style="display:flex;gap:0.4rem;">
                            <a href="{{ route('alumnos.edit', $item) }}" class="btn">Editar</a>
                            <form action="{{ route('alumnos.destroy', $item) }}" method="POST" style="margin:0;" onsubmit="return confirm('¿Eliminar este registro?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No hay alumnos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif
@endsection