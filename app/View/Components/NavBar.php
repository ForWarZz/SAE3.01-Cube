<?php

namespace App\View\Components;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NavBar extends Component
{
    /**
     * @var Category[] $categories
     */
    public array $categories;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->categories = Category::all()->whereNull('id_categorie_parent')->reverse()->all();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.nav-bar', [
            'categories' => $this->categories
        ]);
    }
}
