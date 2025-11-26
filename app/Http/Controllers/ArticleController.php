<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(
        protected ArticleService $articleService,
    )
    { }

    public function search(Request $request)
    {
        $search = $request->input('search', '');
        $articles = $this->articleService->searchArticles($search);

        return response()->json($articles);
    }
}
