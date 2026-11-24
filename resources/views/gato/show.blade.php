<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partida #{{ $partida->id }} — Juego del Gato</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #0f172a;
            color: #f8fafc;
            display: flex;
            justify-content: center;
            padding: 1.5rem;
            margin: 0;
        }
        .contenedor {
            width: 100%;
            max-width: 440px;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 1.4rem;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.9rem;
            font-size: 0.9rem;
        }
        .jugadores {
            font-size: 0.85rem;
            color: #cbd5e1;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 0.65rem 0.85rem;
            margin-bottom: 0.9rem;
            line-height: 1.5;
        }
        .estado {
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            padding: 0.7rem 0.9rem;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 600;
        }
        .tablero {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.6rem;
            margin-bottom: 1rem;
        }
        .casilla-form {
            margin: 0;
        }
        .casilla {
            width: 100%;
            aspect-ratio: 1 / 1;
            font-size: 2rem;
            font-weight: 700;
            border-radius: 10px;
            border: 1px solid #475569;
            background: #0f172a;
            color: #f8fafc;
            cursor: pointer;
        }
        .casilla:hover:not(:disabled) {
            border-color: #38bdf8;
            background: #172554;
        }
        .casilla:disabled {
            cursor: default;
            opacity: 0.85;
        }
        .acciones {
            display: flex;
            gap: 0.6rem;
        }
        .btn {
            flex: 1;
            padding: 0.65rem 0.8rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .btn-muted { background: #334155; color: #f8fafc; }
        .btn-danger { background: #dc2626; color: #fff; }
        .alerta-ok {
            background: #064e3b;
            color: #a7f3d0;
            padding: 0.6rem 0.8rem;
            border-radius: 8px;
            margin-bottom: 0.8rem;
            font-size: 0.88rem;
        }
        .alerta-err {
            background: #7f1d1d;
            color: #fecaca;
            padding: 0.6rem 0.8rem;
            border-radius: 8px;
            margin-bottom: 0.8rem;
            font-size: 0.88rem;
        }
    </style>
</head>
<body>
@php
    $userId = auth()->id();
    $esMiTurno = $partida->estado === 'en_curso' && (int) $partida->turno_user_id === (int) $userId;
    $miSimbolo = (int) $partida->jugador_x_id === (int) $userId ? 'X' : ((int) $partida->jugador_o_id === (int) $userId ? 'O' : 'Espectador');
@endphp

<div class="contenedor">
    <div class="topbar">
        <strong>Partida #{{ $partida->id }}</strong>
        <a href="{{ route('gato.index') }}" style="color:#38bdf8; text-decoration:none;">← Lobby / Historial</a>
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

    <div class="jugadores">
        <div><strong>X:</strong> {{ $partida->jugadorX?->name ?? '—' }}</div>
        <div><strong>O:</strong> {{ $partida->jugadorO?->name ?? 'Esperando que se una un jugador...' }}</div>
        <div><strong>Tu rol en esta partida:</strong> {{ $miSimbolo }}</div>
    </div>

    <div class="estado" id="estado-partida">
        @if ($partida->estado === 'esperando')
            Esperando a que otro jugador se una como <strong>O</strong>...
        @elseif ($partida->estado === 'en_curso')
            Turno de <strong>{{ $partida->turno_simbolo }}</strong>
            ({{ $partida->turnoUsuario?->name ?? '—' }})
            — {{ $esMiTurno ? '¡Te toca tirar!' : 'Esperando movimiento rival...' }}
        @elseif ($partida->estado === 'gana_x')
            Ganó <strong>X</strong> ({{ $partida->jugadorX?->name }})
        @elseif ($partida->estado === 'gana_o')
            Ganó <strong>O</strong> ({{ $partida->jugadorO?->name }})
        @elseif ($partida->estado === 'empate')
            ¡Empate!
        @elseif ($partida->estado === 'abandonada')
            Partida abandonada por {{ $partida->abandonadaPor?->name ?? 'un jugador' }}
        @elseif ($partida->estado === 'cancelada')
            Partida cancelada por administración
        @endif
    </div>

    <div class="tablero" id="tablero-gato">
        @foreach ($partida->tablero ?? array_fill(0, 9, null) as $indice => $valor)
            <form class="casilla-form" action="{{ route('gato.jugar', $partida) }}" method="POST">
                @csrf
                <input type="hidden" name="posicion" value="{{ $indice }}">
                <button
                    type="submit"
                    class="casilla"
                    data-indice="{{ $indice }}"
                    @if ($valor !== null || ! $esMiTurno) disabled @endif
                >
                    {{ $valor ?? '' }}
                </button>
            </form>
        @endforeach
    </div>

    <div class="acciones">
        <a href="{{ route('gato.index') }}" class="btn btn-muted">Volver al lobby</a>
        @if (in_array($partida->estado, ['esperando', 'en_curso'], true) && $miSimbolo !== 'Espectador')
            <form action="{{ route('gato.abandonar', $partida) }}" method="POST" style="flex:1; margin:0;">
                @csrf
                <button type="submit" class="btn btn-danger" style="width:100%;">Abandonar</button>
            </form>
        @endif
    </div>
</div>

<script>
(() => {
    const userId = {{ (int) auth()->id() }};
    const estadoUrl = @json(route('gato.estado', $partida));
    const estadoEl = document.getElementById('estado-partida');
    const botones = Array.from(document.querySelectorAll('#tablero-gato .casilla'));
    let ultimoEstado = @json($partida->estado);
    let ultimoTurnoUserId = @json($partida->turno_user_id);
    let ultimoTablero = @json($partida->tablero ?? array_fill(0, 9, null));

    async function refrescarPartida() {
        try {
            const resp = await fetch(estadoUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!resp.ok) return;
            const data = await resp.json();

            const cambioEstado = data.estado !== ultimoEstado;
            const cambioTurno = data.turno_user_id !== ultimoTurnoUserId;
            const cambioTablero = JSON.stringify(data.tablero) !== JSON.stringify(ultimoTablero);

            if (cambioEstado || cambioTurno || cambioTablero) {
                ultimoEstado = data.estado;
                ultimoTurnoUserId = data.turno_user_id;
                ultimoTablero = data.tablero;

                botones.forEach((btn, idx) => {
                    const val = data.tablero[idx] ?? '';
                    btn.textContent = val;
                    const esMiTurno = data.estado === 'en_curso' && Number(data.turno_user_id) === Number(userId);
                    btn.disabled = val !== '' || !esMiTurno;
                });

                if (data.estado === 'esperando') {
                    estadoEl.innerHTML = 'Esperando a que otro jugador se una como <strong>O</strong>...';
                } else if (data.estado === 'en_curso') {
                    const miTurno = Number(data.turno_user_id) === Number(userId);
                    estadoEl.innerHTML = `Turno de <strong>${data.turno_simbolo}</strong> (${data.turno_nombre ?? '—'}) — ${miTurno ? '¡Te toca tirar!' : 'Esperando movimiento rival...'}`;
                } else if (data.estado === 'gana_x') {
                    estadoEl.innerHTML = `Ganó <strong>X</strong> (${data.jugador_x ?? ''})`;
                } else if (data.estado === 'gana_o') {
                    estadoEl.innerHTML = `Ganó <strong>O</strong> (${data.jugador_o ?? ''})`;
                } else if (data.estado === 'empate') {
                    estadoEl.innerHTML = '¡Empate!';
                } else if (data.estado === 'abandonada') {
                    estadoEl.innerHTML = 'Partida abandonada.';
                } else if (data.estado === 'cancelada') {
                    estadoEl.innerHTML = 'Partida cancelada por administración.';
                }
            }
        } catch (_) {}
    }

    setInterval(refrescarPartida, 2000);
})();
</script>
</body>
</html>