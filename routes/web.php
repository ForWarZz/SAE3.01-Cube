<?php

use App\Http\Controllers\AdresseController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopController;
use App\Models\Category;
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
    $bike = Category::firstWhere('nom_categorie', 'Vélos');
    $accessory = Category::firstWhere('nom_categorie', 'Accessoires');

    return view('index', [
        'bikeCategoryId' => $bike?->id_categorie,
        'accessoryCategoryId' => $accessory?->id_categorie,
    ]);
})->name('home');

Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/search', [ArticleController::class, 'search'])->name('search');

    Route::get('/categories/{category}', [ArticleController::class, 'viewByCategory'])->name('by-category');
    Route::get('/modeles/{model}', [ArticleController::class, 'viewByModel'])->name('by-model');

    Route::get('/{article}/360/{color?}', [ArticleController::class, 'show360'])->name('view-360');
    Route::get('/{article}/{reference}', [ArticleController::class, 'showByRef'])->name('show-reference');

    Route::get('/{article}', [ArticleController::class, 'show'])->name('show');
});

Route::prefix('panier')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::delete('/', [CartController::class, 'delete'])->name('delete');
    Route::patch('/quantity', [CartController::class, 'updateQuantity'])->name('update-quantity');
    Route::post('/add', [CartController::class, 'addToCart'])->name('add');

    Route::prefix('/code-promo')->name('discount.')->group(function () {
        Route::post('/', [CartController::class, 'applyDiscount'])->name('apply');
        Route::delete('/', [CartController::class, 'clearDiscount'])->name('remove');
    });

    Route::get('/validation', [OrderController::class, 'checkout'])->name('checkout')
        ->middleware('auth');
});

Route::middleware('auth')->prefix('tableau-de-bord')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::prefix('adresses')->name('adresses.')->group(function () {
        Route::get('/', [AdresseController::class, 'index'])->name('index');
        Route::get('/nouvelle', [AdresseController::class, 'create'])->name('create');
        Route::post('/', [AdresseController::class, 'store'])->name('store');
        Route::delete('/{adresse}', [AdresseController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show'); 
        Route::post('/', [OrderController::class, 'store'])->name('store');
        Route::get('/validation', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/livraison', [OrderController::class, 'updateOrder'])->name('update-shipping');
    });
});

Route::middleware('auth')->group(function () {
    Route::prefix('commande')->name('checkout.')->group(function () {
        Route::get('/validation', [OrderController::class, 'checkout'])->name('index');
        Route::post('/livraison', [OrderController::class, 'updateOrder'])->name('update-shipping');
    });

    Route::prefix('paiement')->name('payment.')->group(function () {
        Route::post('/', [CheckoutController::class, 'checkout'])->name('process');
        Route::get('/succes', [CheckoutController::class, 'success'])->name('success');
        Route::get('/erreur', [CheckoutController::class, 'cancel'])->name('cancel');
    });
});

Route::get('/shops', [ShopController::class, 'index'])->name('shops.index');
Route::post('/shop/select', [ShopController::class, 'select'])->name('shop.select');
Route::get('/availability/{reference}', [AvailabilityController::class, 'show'])->name('availability.show');

require __DIR__.'/auth.php';
