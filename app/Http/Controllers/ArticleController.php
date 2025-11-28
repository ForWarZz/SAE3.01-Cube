<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Bike;
use App\Models\BikeFrame;
use App\Models\BikeFrameMaterial;
use App\Models\BikeModel;
use App\Models\BikeReference;
use App\Models\Category;
use App\Models\Characteristic;
use App\Models\CharacteristicType;
use App\Models\Color;
use App\Models\Usage;
use App\Models\Vintage;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function __construct(
        protected ArticleService $articleService,
    )
    { }

    private function getFilterData($baseQuery)
    {
        // Get article IDs from the base query (without filters applied)
        $articleIds = (clone $baseQuery)->pluck('id_article');
        
        // Get vintages used by bikes in the current list
        $usedVintageIds = Bike::whereIn('id_article', $articleIds)
            ->distinct()
            ->pluck('id_millesime');
        
        // Get colors used by bike references in the current list
        $usedColorIds = BikeReference::whereIn('id_article', $articleIds)
            ->distinct()
            ->pluck('id_couleur');
        
        // Get frame types used by bike references in the current list
        $usedFrameIds = BikeReference::whereIn('id_article', $articleIds)
            ->distinct()
            ->pluck('id_cadre_velo');
        
        // Get materials used by bikes in the current list
        $usedMaterialIds = Bike::whereIn('id_article', $articleIds)
            ->distinct()
            ->pluck('id_materiau_cadre');
        
        // Get usages used by bikes in the current list
        $usedUsageIds = Bike::whereIn('id_article', $articleIds)
            ->distinct()
            ->pluck('id_usage');
        
        // Check if there are any articles with promotions
        $hasPromotions = (clone $baseQuery)->where('pourcentage_remise', '>', 0)->exists();

        // Get weight (poids) range from characteristics - specifically "Poids du vélo"
        $poidsCharacteristic = Characteristic::where('nom_caracteristique', 'Poids du vélo')->first();
        $weightRange = ['min' => 0, 'max' => 50];
        if ($poidsCharacteristic) {
            $weights = DB::table('caracterise')
                ->where('id_caracteristique', $poidsCharacteristic->id_caracteristique)
                ->whereIn('id_article', $articleIds)
                ->pluck('valeur_caracteristique')
                ->map(fn($v) => floatval(str_replace(',', '.', preg_replace('/[^0-9,.]/', '', $v))))
                ->filter(fn($v) => $v > 0);
            
            if ($weights->count() > 0) {
                $weightRange = [
                    'min' => floor($weights->min()),
                    'max' => ceil($weights->max()),
                ];
            }
        }

        return [
            'vintages' => Vintage::whereIn('id_millesime', $usedVintageIds)
                ->orderBy('millesime_velo', 'desc')->get(),
            'frames' => BikeFrame::whereIn('id_cadre_velo', $usedFrameIds)
                ->orderBy('label_cadre_velo')->get(),
            'materials' => BikeFrameMaterial::whereIn('id_materiau_cadre', $usedMaterialIds)
                ->orderBy('label_materiau_cadre')->get(),
            'colors' => Color::whereIn('id_couleur', $usedColorIds)
                ->orderBy('label_couleur')->get(),
            'usages' => Usage::whereIn('id_usage', $usedUsageIds)
                ->orderBy('label_usage')->get(),
            'hasPromotions' => $hasPromotions,
            'priceRange' => [
                'min' => (clone $baseQuery)->min('prix_article') ?? 0,
                'max' => (clone $baseQuery)->max('prix_article') ?? 10000,
            ],
            'weightRange' => $weightRange,
        ];
    }

    public function search(Request $request)
    {
        $search = $request->input('search', '');
        $sortBy = $request->input('sort_by', 'name_asc');
        $articles = $this->articleService->searchArticles($search, $sortBy);

        return view('article.search', [
            'articles' => $articles,
            'search' => $search,
            'sortBy' => $sortBy,
            'pageTitle' => 'Résultats de recherche : ' . $search,
            'breadcrumbs' => [
                ['label' => 'Accueil', 'url' => route('home')],
                ['label' => 'Recherche', 'url' => null],
            ]
        ]);
    }

    public function viewByCategory(Category $category, Request $request)
    {
        $sortBy = $request->input('sort_by', 'name_asc');
        $filters = $request->only(['millesime', 'cadre', 'prix_max', 'materiau', 'couleur', 'usage', 'promotion', 'poids_max']);
        
        // Base query without filters for getting filter options
        $baseQuery = Article::whereIn('id_categorie', $category->getAllChildrenIds());
        
        $query = Article::whereIn('id_categorie', $category->getAllChildrenIds());
        $query = $this->articleService->applyFilters($query, $filters);
        $query = $this->articleService->applySorting($query, $sortBy);
        
        $articles = $query->paginate(15)->appends(array_merge(['sort_by' => $sortBy], $filters));

        return view("article.index", array_merge([
            'articles' => $articles,
            'sortBy' => $sortBy,
            'filters' => $filters,
            'pageTitle' => $category->nom_categorie,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home')],
                ['label' => $category->nom_categorie, 'url' => null],
            ]
        ], $this->getFilterData($baseQuery)));
    }

    public function viewByModel(BikeModel $bikeModel, Request $request)
    {
        $sortBy = $request->input('sort_by', 'name_asc');
        $filters = $request->only(['millesime', 'cadre', 'prix_max', 'materiau', 'couleur', 'usage', 'promotion', 'poids_max']);
        
        // Base query without filters for getting filter options
        $baseQuery = Article::query()->whereHas('bike', function($query) use ($bikeModel) {
            $query->where('id_modele_velo', '=', $bikeModel->id_modele_velo);
        });
        
        $query = Article::query()->whereHas('bike', function($query) use ($bikeModel) {
            $query->where('id_modele_velo', '=', $bikeModel->id_modele_velo);
        });
        
        $query = $this->articleService->applyFilters($query, $filters);
        $query = $this->articleService->applySorting($query, $sortBy);
        
        $articles = $query->paginate(15)->appends(array_merge(['sort_by' => $sortBy], $filters));

        return view("article.index", array_merge([
            'articles' => $articles,
            'sortBy' => $sortBy,
            'filters' => $filters,
            'pageTitle' => $bikeModel->nom_modele_velo,
            'breadcrumbs' => [
                ['label' => 'Home', 'url' => route('home')],
                ['label' => $bikeModel->nom_modele_velo, 'url' => null],
            ]
        ], $this->getFilterData($baseQuery)));
    }

    public function show(Article $article)
    {
        $article->load(['bike', 'accessories']);

        if ($article->bike()->exists()) {
            return redirect()->route(
                'articles.bikes.redirect-to-default',
                ['bike' => $article->bike]
            );
        }
    }
}
