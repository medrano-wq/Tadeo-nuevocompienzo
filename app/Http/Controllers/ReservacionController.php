<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Reservacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservacionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $esAdmin = in_array($user->role?->slug, ['admin', 'administrador'], true);

        $query = Reservacion::with(['equipo', 'usuario'])->latest('fecha_reserva');

        if (! $esAdmin) {
            $query->where('user_id', $user->id);
        }

        $reservaciones = $query->paginate(10)->withQueryString();

        return view('reservaciones.index', compact('reservaciones', 'esAdmin'));
    }

    public function show(Request $request, Reservacion $reservacion)
    {
        $this->authorizeAccess($request, $reservacion);

        $reservacion->load(['equipo', 'usuario']);

        return view('reservaciones.show', compact('reservacion'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipo_id' => ['required', 'integer', 'exists:equipos,id'],
            'fecha_reserva' => ['nullable', 'date'],
        ]);

        $user = $request->user();

        try {
            $reservacion = DB::transaction(function () use ($validated, $user) {
                $equipo = Equipo::where('id', $validated['equipo_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($equipo->estado !== 'Disponible') {
                    throw new \RuntimeException('El equipo no está disponible para reservación.');
                }

                $reservacionActiva = Reservacion::where('equipo_id', $equipo->id)
                    ->where('estado', 'Activa')
                    ->lockForUpdate()
                    ->exists();

                if ($reservacionActiva) {
                    throw new \RuntimeException('El equipo ya cuenta con una reservación activa.');
                }

                $equipo->update(['estado' => 'Reservado']);

                return Reservacion::create([
                    'user_id' => $user->id,
                    'equipo_id' => $equipo->id,
                    'fecha_reserva' => $validated['fecha_reserva'] ?? now(),
                    'estado' => 'Activa',
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['equipo_id' => $e->getMessage()]);
        }

        return redirect()->route('reservaciones.show', $reservacion)
            ->with('status', 'Reservación creada correctamente.');
    }

    public function cancel(Request $request, Reservacion $reservacion)
    {
        $this->authorizeAccess($request, $reservacion);

        try {
            DB::transaction(function () use ($reservacion) {
                $res = Reservacion::where('id', $reservacion->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($res->estado !== 'Activa') {
                    throw new \RuntimeException('Solo se pueden cancelar reservaciones activas.');
                }

                $equipo = Equipo::where('id', $res->equipo_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $res->update(['estado' => 'Cancelada']);

                $hayOtrasActivas = Reservacion::where('equipo_id', $equipo->id)
                    ->where('estado', 'Activa')
                    ->exists();

                if (! $hayOtrasActivas && $equipo->estado === 'Reservado') {
                    $equipo->update(['estado' => 'Disponible']);
                }
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['reservacion' => $e->getMessage()]);
        }

        return redirect()->route('reservaciones.index')
            ->with('status', 'Reservación cancelada y equipo liberado.');
    }

    private function authorizeAccess(Request $request, Reservacion $reservacion): void
    {
        $user = $request->user();
        $esAdmin = in_array($user->role?->slug, ['admin', 'administrador'], true);

        if (! $esAdmin && $reservacion->user_id !== $user->id) {
            abort(403, 'No tienes permiso para acceder a esta reservación.');
        }
    }
}