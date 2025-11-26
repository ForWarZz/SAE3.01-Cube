<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\VeloController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Velo;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [CategorieController::class, 'index'])->name('home');

Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/{article}', [ArticleController::class, 'show'])->name('show');

    Route::get('/categories/{categorie}', [ArticleController::class, 'viewByCat'])->name('by-category');
    Route::get('/modeles/{model}', [ArticleController::class, 'viewByModel'])->name('by-model');

    Route::prefix('/velos')->name('bikes.')->group(function () {
        Route::get('/reference/{reference}', [VeloController::class, 'show'])->name('show');
        Route::get('/{bike}', [VeloController::class, 'redirectToDefaultVariant'])->name('redirect-to-default');
    });
});

//Route::get('/tableau-de-bord', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');
//
//Route::middleware('auth')->prefix('profil')->name('profile.')->group(function () {
//    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
//    Route::patch('/', [ProfileController::class, 'update'])->name('update');
//    Route::delete('/', [ProfileController::class, 'destroy'])->name('delete');
//});

require __DIR__.'/auth.php';
