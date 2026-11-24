@php
    $esDisponible = $estado === 'Disponible';
    $estilo = $esDisponible
        ? 'background:#dcfce7;color:#166534;border:1px solid #86efac;'
        : 'background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;';
@endphp

<span style="display:inline-block;padding:2px 8px;border-radius:6px;font-weight:700;font-size:0.85rem;{{ $estilo }}">
    {{ $estado }}
</span>