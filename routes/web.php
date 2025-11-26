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

Route::get('/', [CategorieController::class, 'index']);

Route::prefix('/velos')->group(function () {
    Route::get('/{reference}', [VeloController::class, 'show'])->name('velo.show');
    Route::get('/velo/{velo}', [VeloController::class, 'redirectToDefaultVariant'])->name('velo.redirectToFirstRef');
});

Route::get('/categorie/{categorie}', [ArticleController::class, "viewByCat"])->name('viewByCat');
Route::get('/modele/{model}', [ArticleController::class, "viewByModel"])->name('viewByModel');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
