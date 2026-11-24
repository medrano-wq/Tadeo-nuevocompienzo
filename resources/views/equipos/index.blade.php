{{-- resources/views/equipos/index.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Equipos — Reservaciones</title>
</head>
<body>
    <header>
        <p>Usuario activo: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role?->slug }})</p>
        <nav>
            <a href="{{ route('equipos.index') }}">Equipos</a> |
            <a href="{{ route('reservaciones.index') }}">Mis / Reservaciones</a>
            @if($esAdmin)
                | <a href="{{ route('equipos.create') }}">Nuevo Equipo</a>
            @endif
        </nav>
    </header>

    @if(session('status'))
        <p><strong>{{ session('status') }}</strong></p>
    @endif

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @if($esAdmin)
        <form method="GET" action="{{ route('equipos.index') }}">
            <label for="estado">Filtrar por estado:</label>
            <select name="estado" id="estado" onchange="this.form.submit()">
                <option value="">Todos</option>
                @foreach(['Disponible', 'Reservado', 'Mantenimiento'] as $st)
                    <option value="{{ $st }}" @selected(request('estado') === $st)>{{ $st }}</option>
                @endforeach
            </select>
        </form>
    @endif

    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Especificaciones</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equipos as $equipo)
                <tr>
                    <td>{{ $equipo->codigo }}</td>
                    <td>{{ $equipo->nombre }}</td>
                    <td>{{ $equipo->ubicacion ?? '—' }}</td>
                    <td>{{ $equipo->especificaciones ?? '—' }}</td>
                    <td>{{ $equipo->estado }}</td>
                    <td>
                        @if($equipo->estado === 'Disponible')
                            <form method="POST" action="{{ route('reservaciones.store') }}" style="display:inline;">
                                @csrf
                                <input type="hidden" name="equipo_id" value="{{ $equipo->id }}">
                                <button type="submit">Reservar</button>
                            </form>
                        @endif

                        @if($esAdmin)
                            <a href="{{ route('equipos.edit', $equipo) }}">Editar</a>
                            <form method="POST" action="{{ route('equipos.destroy', $equipo) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Eliminar equipo?')">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No hay equipos registrados o disponibles.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $equipos->links() }}
</body>
</html>