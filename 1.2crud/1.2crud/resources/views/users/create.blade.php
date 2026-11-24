<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>
</head>
<body>
    <h1>Registrar usuario</h1>

    <p>
        <a href="{{ route('users.index') }}">Volver a usuarios</a>
    </p>

    @if ($errors->any())
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <div>
            <label for="name">Nombre</label><br>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>
        </div>
        <br>

        <div>
            <label for="email">Correo electrónico</label><br>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        </div>
        <br>

        <div>
            <label for="password">Contraseña</label><br>
            <input id="password" type="password" name="password" required>
        </div>
        <br>

        <div>
            <label for="password_confirmation">Confirmar contraseña</label><br>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>
        <br>

        <div>
            <label for="role_id">Rol</label><br>
            <select id="role_id" name="role_id">
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected((string) old('role_id') === (string) $role->id)>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <br>

        <button type="submit">Guardar usuario</button>
    </form>
</body>
</html>