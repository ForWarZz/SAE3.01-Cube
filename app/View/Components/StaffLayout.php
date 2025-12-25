<?php

namespace App\View\Components;

use Auth;
use Illuminate\View\Component;
use Illuminate\View\View;

class StaffLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        $staffUser = Auth::guard('staff')->user();

        return view('layouts.staff', [
            'isCommercial' => $staffUser->isCommercial(),
            'isDirector' => $staffUser->isCommercialDirector(),
            'isDpo' => $staffUser->isDpo(),
        ]);
    }
}
