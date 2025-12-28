<?php

namespace App\View\Components;

use App\Models\BikeModel;
use App\Models\Category;
use App\Services\Cart\CartSessionManager;
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

    public function __construct(
        protected CartSessionManager $cartSession,
    ) {
        $allCategories = Category::select('id_categorie', 'id_categorie_parent', 'nom_categorie')
            ->orderBy('id_categorie', 'desc')
            ->get();

        $modelsByCategoryId = BikeModel::query()
            ->select('modele_velo.id_modele_velo', 'modele_velo.nom_modele_velo', 'article.id_categorie')
            ->join('velo', 'modele_velo.id_modele_velo', '=', 'velo.id_modele_velo')
            ->join('article', 'velo.id_article', '=', 'article.id_article')
            ->whereNull('article.deleted_at')
            ->distinct()
            ->get()
            ->groupBy('id_categorie');

        foreach ($allCategories as $cat) {
            $cat->models = $modelsByCategoryId[$cat->id_categorie] ?? collect();
            $cat->children = collect();
        }

        $this->categories = $this->buildTree($allCategories, null);
        $this->cartItemCount = count($this->cartSession->getItems());
    }

    private function buildTree(Collection $cats, ?int $parentId): Collection
    {
        return $cats->where('id_categorie_parent', $parentId)->map(function ($cat) use ($cats) {
            $cat->children = $this->buildTree($cats, $cat->id_categorie);

            return $cat;
        })->values();
    }

    public function render(): View|Closure|string
    {
        return view('components.nav-bar', [
            'categories' => $this->categories,
            'cartItemCount' => $this->cartItemCount,
        ]);
    }
}
