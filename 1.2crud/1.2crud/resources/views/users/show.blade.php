<!-- resources/views/users/show.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Detalle de usuario</title></head>
<body>
    <h1>Detalle de usuario</h1>
    <a href="{{ route('users.index') }}">Volver</a>
    <p><strong>ID:</strong> {{ $user->id }}</p>
    <p><strong>Nombre:</strong> {{ $user->name }}</p>
    <p><strong>Correo:</strong> {{ $user->email }}</p>
    <p><strong>Rol:</strong> {{ $user->role?->name ?? 'N/A' }}</p>
    <p><strong>Creado:</strong> {{ $user->created_at }}</p>
</body>
</html>