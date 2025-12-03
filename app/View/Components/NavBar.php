<?php

namespace App\View\Components;

use App\Models\Category;
use App\Services\CartService;
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

    public int $cartItemCount = 0;

    /**
     * Create a new component instance.
     */
    public function __construct(
        protected CartService $cartService,
    ) {
        $this->categories = Category::query()
            ->with([
                'childrenRecursive',
                'articles.bike.bikeModel',
            ])
            ->orderBy('id_categorie', 'desc')
            ->whereNull('id_categorie_parent')
            ->get();

        $this->cartItemCount = count($this->cartService->getCartFromSession());
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.nav-bar', [
            'categories' => $this->categories,
            'cartItemCount' => $this->cartItemCount,
        ]);
    }
}
