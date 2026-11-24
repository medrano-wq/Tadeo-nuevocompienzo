@php
    $styles = match ($equivalencia) {
        'AU' => 'background:#dcfce7;color:#166534;border:1px solid #86efac;',
        'DE' => 'background:#dbeafe;color:#1e40af;border:1px solid #93c5fd;',
        'SA' => 'background:#fef9c3;color:#854d0e;border:1px solid #fde047;',
        default => 'background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;',
    };
@endphp

<span style="display:inline-block;padding:2px 8px;border-radius:6px;font-weight:700;font-size:0.85rem;{{ $styles }}">
    {{ $equivalencia }}
</span>