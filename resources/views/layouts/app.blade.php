<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema Académico')</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 2rem; color: #111827; background: #f9fafb; }
        .container { max-width: 960px; margin: 0 auto; background: #fff; padding: 1.5rem; border-radius: 8px; border: 1px solid #e5e7eb; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.65rem; border-bottom: 1px solid #e5e7eb; text-align: left; }
        .btn { display: inline-block; padding: 0.45rem 0.8rem; border-radius: 6px; text-decoration: none; border: 1px solid #d1d5db; background: #fff; color: #111827; cursor: pointer; font-size: 0.9rem; }
        .btn-primary { background: #1d4ed8; color: #fff; border-color: #1d4ed8; }
        .btn-danger { background: #dc2626; color: #fff; border-color: #dc2626; }
        .alert-success { background: #dcfce7; color: #166534; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 0.75rem; border-radius: 6px; margin-bottom: 1rem; }
        .field { margin-bottom: 1rem; }
        .field label { display: block; margin-bottom: 0.35rem; font-weight: 600; }
        .field input { width: 100%; max-width: 420px; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; }
    </style>
</head>
<body>
    <div class="container">
        @auth
            <div class="topbar">
                <div>
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>({{ strtoupper(auth()->user()->role?->slug ?? 'sin rol') }})</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn">Cerrar sesión</button>
                </form>
            </div>
        @endauth

        @if (session('status'))
            <div class="alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-error">
                <ul style="margin:0;padding-left:1.2rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>