<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
</head>
<body>
    <p>
        Usuario: <strong>{{ auth()->user()->name }}</strong>
        ({{ auth()->user()->role?->name ?? 'Sin rol' }})
    </p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>

    @if (session('status'))
        <p><strong>{{ session('status') }}</strong></p>
    @endif

    <h1>Usuarios activos</h1>
    <a href="{{ route('users.create') }}">Registrar usuario</a>

    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role?->name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('users.show', $user) }}">Ver</a>
                        <a href="{{ route('users.edit', $user) }}">Editar</a>

                        @if (auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Soft Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (auth()->user()->isAdmin())
        <h2>Usuarios eliminados (Soft Delete)</h2>
        <table border="1" cellpadding="6">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Eliminado en</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($trashedUsers as $trashed)
                    <tr>
                        <td>{{ $trashed->id }}</td>
                        <td>{{ $trashed->name }}</td>
                        <td>{{ $trashed->email }}</td>
                        <td>{{ $trashed->deleted_at }}</td>
                        <td>
                            <form method="POST" action="{{ route('users.restore', $trashed->id) }}" style="display:inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit">Restaurar</button>
                            </form>

                            <form method="POST" action="{{ route('users.force-delete', $trashed->id) }}" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Eliminar permanente</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">Sin registros eliminados.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif
</body>
</html>