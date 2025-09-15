<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Monitoring extends Component
{
    /**
     * Create a new component instance.
     */

     public $appreance;
    public function __construct($appreance = null)
    {
        $this->appreance = $appreance;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('layouts.monitoring');
    }
}
