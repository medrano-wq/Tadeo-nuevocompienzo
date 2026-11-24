=<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservaciones</title>
</head>
<body>
    <header>
        <p>Usuario activo: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role?->slug }})</p>
        <nav>
            <a href="{{ route('equipos.index') }}">Equipos</a> |
            <a href="{{ route('reservaciones.index') }}">Reservaciones</a>
        </nav>
    </header>

    @if(session('status'))
        <p><strong>{{ session('status') }}</strong></p>
    @endif

    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>ID</th>
                <th>Equipo</th>
                @if($esAdmin)
                    <th>Usuario</th>
                @endif
                <th>Fecha Reservación</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservaciones as $res)
                <tr>
                    <td>#{{ $res->id }}</td>
                    <td>{{ $res->equipo?->codigo }} — {{ $res->equipo?->nombre }}</td>
                    @if($esAdmin)
                        <td>{{ $res->usuario?->name }} ({{ $res->usuario?->email }})</td>
                    @endif
                    <td>{{ $res->fecha_reserva?->format('Y-m-d H:i') }}</td>
                    <td>{{ $res->estado }}</td>
                    <td>
                        <a href="{{ route('reservaciones.show', $res) }}">Detalle</a>
                        @if($res->estado === 'Activa')
                            <form method="POST" action="{{ route('reservaciones.cancel', $res) }}" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit">Cancelar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $esAdmin ? 6 : 5 }}">No hay reservaciones registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $reservaciones->links() }}
</body>
</html>