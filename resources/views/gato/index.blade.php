<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby Gato — Práctica 1.6</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #0f172a;
            color: #f8fafc;
            margin: 0;
            padding: 1.5rem;
        }
        .wrap {
            max-width: 980px;
            margin: 0 auto;
            display: grid;
            gap: 1.25rem;
        }
        .card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 1.25rem;
        }
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        h1, h2 {
            margin: 0 0 0.75rem 0;
            font-size: 1.15rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        th, td {
            padding: 0.6rem;
            border-bottom: 1px solid #334155;
            text-align: left;
        }
        .btn {
            display: inline-block;
            padding: 0.45rem 0.8rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-success { background: #059669; color: #fff; }
        .btn-danger  { background: #dc2626; color: #fff; }
        .btn-muted   { background: #334155; color: #f8fafc; }
        .alerta-ok {
            background: #064e3b;
            color: #a7f3d0;
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }
        .alerta-err {
            background: #7f1d1d;
            color: #fecaca;
            padding: 0.75rem 1rem;
            border-radius: 8px;
        }
        .badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .chip {
            background: #0f172a;
            border: 1px solid #475569;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card header-row">
        <div>
            <h1>Juego del Gato (Multijugador)</h1>
            <small style="color:#94a3b8;">
                Usuario: <strong>{{ auth()->user()->name ?? auth()->user()->email }}</strong>
                @if ($esAdmin) · <strong>Modo Administrador</strong> @endif
            </small>
        </div>
        <form action="{{ route('gato.store') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">+ Crear nueva partida (X)</button>
        </form>
    </div>

    @if (session('status'))
        <div class="alerta-ok">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alerta-err">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card">
        <h2>Mis partidas activas</h2>
        @if ($misPartidas->isEmpty())
            <p style="color:#94a3b8; margin:0;">No tienes partidas activas en este momento.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jugador X</th>
                        <th>Jugador O</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($misPartidas as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->jugadorX?->name ?? '—' }}</td>
                            <td>{{ $p->jugadorO?->name ?? 'Esperando rival...' }}</td>
                            <td>{{ $p->estado }}</td>
                            <td style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                                <a href="{{ route('gato.show', $p) }}" class="btn btn-muted">Abrir tablero</a>
                                <form action="{{ route('gato.abandonar', $p) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Abandonar</button>
                                </form>
                                @if ($esAdmin)
                                    <form action="{{ route('gato.cancelar', $p) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Cancelar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card">
        <h2>Partidas disponibles para unirse</h2>
        @if ($partidasDisponibles->isEmpty())
            <p style="color:#94a3b8; margin:0;">No hay partidas esperando rival por ahora.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Creador (X)</th>
                        <th>Estado</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($partidasDisponibles as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->jugadorX?->name ?? '—' }}</td>
                            <td>{{ $p->estado }}</td>
                            <td>
                                <form action="{{ route('gato.unirse', $p) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Unirse como O</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="card">
        <h2>Jugadores registrados</h2>
        <div class="badges">
            @forelse ($jugadores as $j)
                <span class="chip">{{ $j->name ?? $j->email }}</span>
            @empty
                <span style="color:#94a3b8;">No hay otros usuarios registrados.</span>
            @endforelse
        </div>
    </div>

    <div class="card">
        <h2>{{ $esAdmin ? 'Historial general de partidas' : 'Mi historial de partidas' }}</h2>
        @if ($historial->isEmpty())
            <p style="color:#94a3b8; margin:0;">Aún no hay partidas registradas en el historial.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>X</th>
                        <th>O</th>
                        <th>Estado</th>
                        <th>Ganador</th>
                        @if ($esAdmin)
                            <th>Acción</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($historial as $h)
                        <tr>
                            <td>{{ $h->id }}</td>
                            <td>{{ $h->jugadorX?->name ?? '—' }}</td>
                            <td>{{ $h->jugadorO?->name ?? '—' }}</td>
                            <td>{{ $h->estado }}</td>
                            <td>{{ $h->ganador?->name ?? ($h->estado === 'empate' ? 'Empate' : '—') }}</td>
                            @if ($esAdmin)
                                <td>
                                    @if (in_array($h->estado, ['esperando', 'en_curso'], true))
                                        <form action="{{ route('gato.cancelar', $h) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">Cancelar</button>
                                        </form>
                                    @else
                                        <a href="{{ route('gato.show', $h) }}" class="btn btn-muted">Ver</a>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top:0.75rem;">
                {{ $historial->links() }}
            </div>
        @endif
    </div>
</div>
</body>
</html>