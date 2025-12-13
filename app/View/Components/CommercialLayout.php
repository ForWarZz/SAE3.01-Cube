<?php

namespace App\View\Components;

use App\Models\Commercial;
use Auth;
use Illuminate\View\Component;
use Illuminate\View\View;

class CommercialLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.commercial', [
            'isDirector' => Auth::guard('commercial')->user()->role == Commercial::DIRECTOR_ROLE,
        ]);
    }
}
