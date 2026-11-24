<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle Reservación #{{ $reservacion->id }}</title>
</head>
<body>
    <h1>Reservación #{{ $reservacion->id }}</h1>
    <a href="{{ route('reservaciones.index') }}">Volver a reservaciones</a>

    <p><strong>Estado:</strong> {{ $reservacion->estado }}</p>
    <p><strong>Fecha:</strong> {{ $reservacion->fecha_reserva?->format('Y-m-d H:i:s') }}</p>
    <p><strong>Usuario:</strong> {{ $reservacion->usuario?->name }} ({{ $reservacion->usuario?->email }})</p>
    <p><strong>Equipo:</strong> {{ $reservacion->equipo?->codigo }} — {{ $reservacion->equipo?->nombre }}</p>

    @if($reservacion->estado === 'Activa')
        <form method="POST" action="{{ route('reservaciones.cancel', $reservacion) }}">
            @csrf
            @method('PATCH')
            <button type="submit">Cancelar Reservación</button>
        </form>
    @endif
</body>
</html>