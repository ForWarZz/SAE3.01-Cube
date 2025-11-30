<?php

namespace App\View\Components;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class NavBar extends Component
{
    /**
     * @var Collection<Category>
     */
    public Collection $categories;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->categories = Category::query()
            ->orderBy('nom_categorie', 'ASC')
            ->where('nom_categorie', '!=', 'Accessoires')
            ->whereNull('id_categorie_parent')
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.nav-bar', [
            'categories' => $this->categories,
        ]);
    }
}
