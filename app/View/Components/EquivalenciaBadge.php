<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EquivalenciaBadge extends Component
{
    public function __construct(
        public readonly string $equivalencia
    ) {}

    public function render(): View
    {
        return view('components.equivalencia-badge');
    }
}