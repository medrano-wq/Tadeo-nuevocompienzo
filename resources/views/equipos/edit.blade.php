<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Equipo</title>
</head>
<body>
    <h1>Editar Equipo: {{ $equipo->codigo }}</h1>
    <a href="{{ route('equipos.index') }}">Volver a equipos</a>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('equipos.update', $equipo) }}">
        @csrf
        @method('PUT')
        <p><label>Código: <input type="text" name="codigo" value="{{ old('codigo', $equipo->codigo) }}" required></label></p>
        <p><label>Nombre: <input type="text" name="nombre" value="{{ old('nombre', $equipo->nombre) }}" required></label></p>
        <p><label>Ubicación: <input type="text" name="ubicacion" value="{{ old('ubicacion', $equipo->ubicacion) }}"></label></p>
        <p><label>Especificaciones: <textarea name="especificaciones">{{ old('especificaciones', $equipo->especificaciones) }}</textarea></label></p>
        <p>
            <label>Estado:
                <select name="estado" required>
                    @foreach(['Disponible', 'Reservado', 'Mantenimiento'] as $st)
                        <option value="{{ $st }}" @selected(old('estado', $equipo->estado) === $st)>{{ $st }}</option>
                    @endforeach
                </select>
            </label>
        </p>
        <button type="submit">Actualizar Equipo</button>
    </form>
</body>
</html>