<?php

use App\Http\Controllers\AdresseController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\BikeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('index');
})->name('home');

Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/search', [ArticleController::class, 'search'])->name('search');
    Route::get('/{article}', [ArticleController::class, 'show'])->name('show');

    Route::get('/categories/{category}', [ArticleController::class, 'viewByCategory'])->name('by-category');
    Route::get('/modeles/{model}', [ArticleController::class, 'viewByModel'])->name('by-model');

    Route::get('/{article}/{reference}', [ArticleController::class, 'showByRef'])->name('show-reference');

    //    Route::prefix('/velos')->name('bikes.')->group(function () {
    //        //        Route::get('/reference/{reference}', [BikeController::class, 'show'])->name('show');
    //        Route::get('/{bike}', [BikeController::class, 'redirectToDefaultVariant'])->name('redirect-to-default');
    //    });

});

// Dashboard routes (requires client session)
Route::middleware('client.auth')->prefix('tableau-de-bord')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    
    Route::prefix('adresses')->name('adresses.')->group(function () {
        Route::get('/', [AdresseController::class, 'index'])->name('index');
        Route::get('/nouvelle', [AdresseController::class, 'create'])->name('create');
        Route::post('/', [AdresseController::class, 'store'])->name('store');
        Route::delete('/{adresse}', [AdresseController::class, 'destroy'])->name('destroy');
    });
});

use App\Http\Controllers\ShopController;
use App\Http\Controllers\AvailabilityController;

Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
Route::post('/shop/select', [ShopController::class, 'select'])->name('shop.select');
Route::get('/availability/{reference}', [AvailabilityController::class, 'show'])->name('availability.show');


require __DIR__.'/auth.php';
