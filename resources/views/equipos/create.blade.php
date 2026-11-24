<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Equipo</title>
</head>
<body>
    <h1>Registrar Equipo</h1>
    <a href="{{ route('equipos.index') }}">Volver a equipos</a>

    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('equipos.store') }}">
        @csrf
        <p><label>Código: <input type="text" name="codigo" value="{{ old('codigo') }}" required></label></p>
        <p><label>Nombre: <input type="text" name="nombre" value="{{ old('nombre') }}" required></label></p>
        <p><label>Ubicación: <input type="text" name="ubicacion" value="{{ old('ubicacion') }}"></label></p>
        <p><label>Especificaciones: <textarea name="especificaciones">{{ old('especificaciones') }}</textarea></label></p>
        <p>
            <label>Estado:
                <select name="estado" required>
                    @foreach(['Disponible', 'Reservado', 'Mantenimiento'] as $st)
                        <option value="{{ $st }}" @selected(old('estado', 'Disponible') === $st)>{{ $st }}</option>
                    @endforeach
                </select>
            </label>
        </p>
        <button type="submit">Guardar Equipo</button>
    </form>
</body>
</html>