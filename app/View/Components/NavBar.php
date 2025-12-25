<?php

namespace App\View\Components;

use App\Models\Article;
use App\Models\Bike;
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

    /**
     * Create a new component instance.
     */
    public function __construct(
        protected CartSessionManager $cartSession,
    ) {
        // 1) Charger toutes les catégories (1 requête)
        $allCategories = Category::select('id_categorie', 'id_categorie_parent', 'nom_categorie')
            ->orderBy('id_categorie', 'asc')
            ->get();

        // 2) Charger tous les articles (1 requête)
        $articles = Article::select('id_article', 'id_categorie')
            ->get()
            ->groupBy('id_categorie');

        // 3) Charger tous les vélos (1 requête)
        $bikes = Bike::select('id_article', 'id_modele_velo')
            ->get()
            ->groupBy('id_article');

        // 4) Charger tous les modèles (1 requête)
        $models = BikeModel::select('id_modele_velo', 'nom_modele_velo')
            ->get()
            ->keyBy('id_modele_velo');

        // 5) Construire l'arbre complet en PHP
        $this->categories = $this->buildTree($allCategories, null, $articles, $bikes, $models);

        // Panier
        $this->cartItemCount = count($this->cartSession->getItems());
    }

    /**
     * Construction récursive de l’arbre complet.
     */
    private function buildTree(Collection $cats, ?int $parentId, $articles, $bikes, $models): Collection
    {
        return $cats->where('id_categorie_parent', $parentId)->map(function ($cat) use ($cats, $articles, $bikes, $models) {

            // éléments inclus : enfants, articles, vélos, modèles
            $cat->children = $this->buildTree($cats, $cat->id_categorie, $articles, $bikes, $models);

            // associer articles → vélos → modèles
            $cat->articles = $articles[$cat->id_categorie] ?? collect();

            foreach ($cat->articles as $article) {
                $article->bikes = $bikes[$article->id_article] ?? collect();

                foreach ($article->bikes as $bike) {
                    $bike->model = $models[$bike->id_modele_velo] ?? null;
                }
            }

            return $cat;
        })->values();
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
