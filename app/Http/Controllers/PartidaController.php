<?php

namespace App\Http\Controllers;

use App\Models\Partida;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartidaController extends Controller
{
    private const LINEAS_GANADORAS = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8],
        [0, 3, 6], [1, 4, 7], [2, 5, 8],
        [0, 4, 8], [2, 4, 6],
    ];

    public function index(Request $request)
    {
        $user = $request->user();
        $esAdmin = $this->esAdmin($user);

        $partidasDisponibles = Partida::with(['jugadorX', 'jugadorO'])
            ->where('estado', 'esperando')
            ->where('jugador_x_id', '!=', $user->id)
            ->latest()
            ->get();

        $misPartidas = Partida::with(['jugadorX', 'jugadorO', 'ganador'])
            ->where(function ($q) use ($user) {
                $q->where('jugador_x_id', $user->id)
                    ->orWhere('jugador_o_id', $user->id);
            })
            ->whereIn('estado', ['esperando', 'en_curso'])
            ->latest()
            ->get();

        $historial = Partida::with(['jugadorX', 'jugadorO', 'ganador', 'abandonadaPor'])
            ->when(! $esAdmin, function ($q) use ($user) {
                $q->where(function ($sub) use ($user) {
                    $sub->where('jugador_x_id', $user->id)
                        ->orWhere('jugador_o_id', $user->id);
                });
            })
            ->whereNotIn('estado', ['esperando'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $jugadores = User::query()
            ->where('id', '!=', $user->id)
            ->latest()
            ->take(20)
            ->get();

        return view('gato.index', compact(
            'partidasDisponibles',
            'misPartidas',
            'historial',
            'jugadores',
            'esAdmin'
        ));
    }

    public function show(Request $request, Partida $partida)
    {
        $user = $request->user();
        $this->authorizeParticipanteOAdmin($user, $partida);

        $partida->load(['jugadorX', 'jugadorO', 'turnoUsuario', 'ganador', 'abandonadaPor']);

        return view('gato.show', compact('partida'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $partida = Partida::create([
            'jugador_x_id' => $user->id,
            'jugador_o_id' => null,
            'tablero' => array_fill(0, 9, null),
            'turno_simbolo' => 'X',
            'turno_user_id' => $user->id,
            'estado' => 'esperando',
            'ganador_id' => null,
            'abandonada_por_id' => null,
        ]);

        return redirect()->route('gato.show', $partida)
            ->with('status', 'Partida creada. Esperando a que otro jugador se una como O.');
    }

    public function unirse(Request $request, Partida $partida)
    {
        $user = $request->user();

        try {
            DB::transaction(function () use ($partida, $user) {
                $p = Partida::where('id', $partida->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($p->estado !== 'esperando') {
                    throw new \RuntimeException('La partida ya no está disponible para unirse.');
                }

                if ((int) $p->jugador_x_id === (int) $user->id) {
                    throw new \RuntimeException('No puedes unirte a tu propia partida.');
                }

                if ($p->jugador_o_id !== null) {
                    throw new \RuntimeException('La partida ya tiene un jugador O asignado.');
                }

                $p->update([
                    'jugador_o_id' => $user->id,
                    'estado' => 'en_curso',
                    'turno_simbolo' => 'X',
                    'turno_user_id' => $p->jugador_x_id,
                ]);
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('gato.index')
                ->withErrors(['partida' => $e->getMessage()]);
        }

        return redirect()->route('gato.show', $partida)
            ->with('status', 'Te uniste a la partida como jugador O. ¡Comienza el juego!');
    }

    public function jugar(Request $request, Partida $partida)
    {
        $validated = $request->validate([
            'posicion' => ['required', 'integer', 'between:0,8'],
        ]);

        $user = $request->user();
        $pos = (int) $validated['posicion'];

        try {
            DB::transaction(function () use ($partida, $user, $pos) {
                $p = Partida::where('id', $partida->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($p->estado !== 'en_curso') {
                    throw new \RuntimeException('La partida no está en curso.');
                }

                $esX = (int) $p->jugador_x_id === (int) $user->id;
                $esO = $p->jugador_o_id !== null && (int) $p->jugador_o_id === (int) $user->id;

                if (! $esX && ! $esO) {
                    abort(403, 'No formas parte de esta partida.');
                }

                if ((int) $p->turno_user_id !== (int) $user->id) {
                    throw new \RuntimeException('No es tu turno o ya realizaste tu movimiento.');
                }

                $tablero = $p->tablero ?? array_fill(0, 9, null);

                if ($tablero[$pos] !== null) {
                    throw new \RuntimeException('Esa casilla ya está ocupada.');
                }

                $simboloActual = $esX ? 'X' : 'O';
                $tablero[$pos] = $simboloActual;

                $resultado = $this->evaluarTablero($tablero);

                if ($resultado !== null) {
                    $ganadorId = match ($resultado) {
                        'gana_x' => $p->jugador_x_id,
                        'gana_o' => $p->jugador_o_id,
                        default => null,
                    };

                    $p->update([
                        'tablero' => $tablero,
                        'estado' => $resultado,
                        'ganador_id' => $ganadorId,
                        'turno_user_id' => null,
                    ]);
                } else {
                    $siguienteSimbolo = $simboloActual === 'X' ? 'O' : 'X';
                    $siguienteUserId = $siguienteSimbolo === 'X' ? $p->jugador_x_id : $p->jugador_o_id;

                    $p->update([
                        'tablero' => $tablero,
                        'turno_simbolo' => $siguienteSimbolo,
                        'turno_user_id' => $siguienteUserId,
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('gato.show', $partida)
                ->withErrors(['juego' => $e->getMessage()]);
        }

        return redirect()->route('gato.show', $partida);
    }

    public function abandonar(Request $request, Partida $partida)
    {
        $user = $request->user();

        try {
            DB::transaction(function () use ($partida, $user) {
                $p = Partida::where('id', $partida->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $esX = (int) $p->jugador_x_id === (int) $user->id;
                $esO = $p->jugador_o_id !== null && (int) $p->jugador_o_id === (int) $user->id;

                if (! $esX && ! $esO) {
                    abort(403, 'No puedes abandonar una partida en la que no participas.');
                }

                if (! in_array($p->estado, ['esperando', 'en_curso'], true)) {
                    throw new \RuntimeException('Esta partida ya finalizó.');
                }

                $ganadorId = null;
                if ($p->estado === 'en_curso') {
                    $ganadorId = $esX ? $p->jugador_o_id : $p->jugador_x_id;
                }

                $p->update([
                    'estado' => 'abandonada',
                    'abandonada_por_id' => $user->id,
                    'ganador_id' => $ganadorId,
                    'turno_user_id' => null,
                ]);
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('gato.show', $partida)
                ->withErrors(['juego' => $e->getMessage()]);
        }

        return redirect()->route('gato.index')
            ->with('status', 'Has abandonado la partida.');
    }

    public function cancelar(Request $request, Partida $partida)
    {
        if (! $this->esAdmin($request->user())) {
            abort(403, 'Solo un administrador puede cancelar partidas.');
        }

        DB::transaction(function () use ($partida) {
            $p = Partida::where('id', $partida->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (in_array($p->estado, ['esperando', 'en_curso'], true)) {
                $p->update([
                    'estado' => 'cancelada',
                    'turno_user_id' => null,
                ]);
            }
        });

        return back()->with('status', 'Partida cancelada por administración.');
    }

    public function estadoJson(Request $request, Partida $partida)
    {
        $this->authorizeParticipanteOAdmin($request->user(), $partida);

        $partida->load(['jugadorX', 'jugadorO', 'turnoUsuario', 'ganador', 'abandonadaPor']);

        return response()->json([
            'id' => $partida->id,
            'tablero' => $partida->tablero,
            'turno_simbolo' => $partida->turno_simbolo,
            'turno_user_id' => $partida->turno_user_id,
            'turno_nombre' => $partida->turnoUsuario?->name,
            'estado' => $partida->estado,
            'ganador_id' => $partida->ganador_id,
            'ganador_nombre' => $partida->ganador?->name,
            'jugador_x' => $partida->jugadorX?->name,
            'jugador_o' => $partida->jugadorO?->name,
            'updated_at' => $partida->updated_at?->toIso8601String(),
        ]);
    }

    private function evaluarTablero(array $tablero): ?string
    {
        foreach (self::LINEAS_GANADORAS as [$a, $b, $c]) {
            if (
                isset($tablero[$a], $tablero[$b], $tablero[$c]) &&
                $tablero[$a] !== null &&
                $tablero[$a] === $tablero[$b] &&
                $tablero[$b] === $tablero[$c]
            ) {
                return $tablero[$a] === 'X' ? 'gana_x' : 'gana_o';
            }
        }

        if (! in_array(null, $tablero, true)) {
            return 'empate';
        }

        return null;
    }

    private function esAdmin(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        $slug = $user->role?->slug ?? $user->rol ?? $user->role ?? null;

        return in_array(strtolower((string) $slug), ['admin', 'administrador'], true);
    }

    private function authorizeParticipanteOAdmin(?User $user, Partida $partida): void
    {
        if (! $user) {
            abort(403);
        }

        if ($this->esAdmin($user)) {
            return;
        }

        $participa = (int) $partida->jugador_x_id === (int) $user->id
            || ($partida->jugador_o_id !== null && (int) $partida->jugador_o_id === (int) $user->id);

        if (! $participa) {
            abort(403, 'No tienes permiso para ver esta partida.');
        }
    }
}