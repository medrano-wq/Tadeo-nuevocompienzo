<!-- resources/views/users/edit.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Editar usuario</title></head>
<body>
    <h1>Editar usuario</h1>
    <a href="{{ route('users.index') }}">Volver</a>

    @if ($errors->any())
        <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    @endif

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')
        <div><label>Nombre:</label><input type="text" name="name" value="{{ old('name', $user->name) }}" required></div>
        <div><label>Correo:</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required></div>
        @if (auth()->user()->isAdmin())
            <div><label>Rol:</label>
                <select name="role_id">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected($user->role_id == $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div><label>Nueva contraseña (opcional):</label><input type="password" name="password"></div>
        <div><label>Confirmar contraseña:</label><input type="password" name="password_confirmation"></div>
        <button type="submit">Actualizar</button>
    </form>
</body>
</html>