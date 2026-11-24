<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EstadoProductoBadge extends Component
{
    public function __construct(
        public readonly string $estado
    ) {}

    public function render(): View
    {
        return view('components.estado-producto-badge');
    }
}