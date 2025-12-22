<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Commercial\CommercialAccessoryController;
use App\Http\Controllers\Commercial\CommercialAuthController;
use App\Http\Controllers\Commercial\CommercialBikeController;
use App\Http\Controllers\Commercial\CommercialCategoryController;
use App\Http\Controllers\Commercial\CommercialModelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
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
    $bikeCategoryId = Category::firstWhere('nom_categorie', 'Vélos')->id_categorie;
    $accessoryCategoryId = Category::firstWhere('nom_categorie', 'Accessoires')->id_categorie;

    return view('index', [
        'bikeCategoryId' => $bikeCategoryId,
        'accessoryCategoryId' => $accessoryCategoryId,
    ]);
})->name('home');

Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/search', [ArticleController::class, 'search'])->name('search');
    Route::get('/{article}', [ArticleController::class, 'show'])->name('show');

    Route::get('/categories/{category}', [ArticleController::class, 'viewByCategory'])->name('by-category');
    Route::get('/modeles/{model}', [ArticleController::class, 'viewByModel'])->name('by-model');

    Route::get('/{article}/{reference}', [ArticleController::class, 'showByRef'])->name('show-reference');
});

Route::prefix('panier')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::delete('/', [CartController::class, 'delete'])->name('delete');
    Route::patch('/quantite', [CartController::class, 'updateQuantity'])->name('update-quantity');
    Route::post('/ajouter', [CartController::class, 'addToCart'])->name('add');

    Route::prefix('/code-promo')->name('discount.')->group(function () {
        Route::post('/', [CartController::class, 'applyDiscount'])->name('apply');
        Route::delete('/', [CartController::class, 'clearDiscount'])->name('remove');
    });

    Route::get('/validation', [OrderController::class, 'checkout'])->name('checkout')
        ->middleware('auth');
});

Route::middleware('auth')->prefix('tableau-de-bord')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::prefix('adresses')->name('addresses.')->group(function () {
        Route::get('/', [AddressController::class, 'index'])->name('index');
        Route::get('/nouvelle', [AddressController::class, 'create'])->name('create');
        Route::post('/', [AddressController::class, 'store'])->name('store');
        Route::get('/{adresse}/modifier', [AddressController::class, 'edit'])->name('edit');
        Route::put('/{adresse}', [AddressController::class, 'update'])->name('update');
        Route::delete('/{adresse}', [AddressController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('profil')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/modifier', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/mot-de-passe', [ProfileController::class, 'updatePassword'])->name('password');
        Route::get('/export-donnees', [ProfileController::class, 'exportData'])->name('export');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('commandes')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
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

        Route::post('/checkout/', [CheckoutController::class, 'checkout'])->name('checkout');
    });
});

Route::prefix('commercial')->name('commercial.')->group(function () {
    Route::middleware('guest:commercial')->group(function () {
        Route::get('/login', [CommercialAuthController::class, 'index'])->name('login');
        Route::post('/login', [CommercialAuthController::class, 'login'])->name('login.submit');
    });

    Route::post('/logout', [CommercialAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:commercial')->group(function () {
        Route::get('/dashboard', function () {
            return view('commercial.dashboard');
        })->name('dashboard');

        Route::get('/categories', [CommercialCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CommercialCategoryController::class, 'store'])->name('categories.store');
        Route::get('/modeles', [CommercialModelController::class, 'index'])->name('models.index');
        Route::post('/modeles', [CommercialModelController::class, 'store'])->name('models.store');

        // Gestion des vélos
        Route::prefix('/velos')->name('bikes.')->group(function () {
            Route::get('/', [CommercialBikeController::class, 'index'])->name('index');
            Route::get('/nouveau', [CommercialBikeController::class, 'create'])->name('create');
            Route::post('/', [CommercialBikeController::class, 'store'])->name('store');
            Route::get('/{bike}', [CommercialBikeController::class, 'show'])->name('show');
            Route::delete('/{bike}', [CommercialBikeController::class, 'destroy'])->name('destroy');

            // Gestion des références
            Route::post('/{bike}/references', [CommercialBikeController::class, 'addReference'])->name('references.store');
            Route::delete('/{bike}/references/{reference}', [CommercialBikeController::class, 'deleteReference'])->name('references.destroy');

            // Gestion des images des références
            Route::post('/{bike}/references/{reference}/images', [CommercialBikeController::class, 'addReferenceImages'])->name('references.images.store');
            Route::delete('/{bike}/references/{reference}/images/{imageName}', [CommercialBikeController::class, 'deleteReferenceImage'])->name('references.images.destroy');
        });

        Route::prefix('/accessoires')->name('accessories.')->group(function () {
            Route::get('/', [CommercialAccessoryController::class, 'index'])->name('index');
            Route::get('/{accessory}/modifier', [CommercialAccessoryController::class, 'edit'])->name('edit');
            Route::put('/{accessory}', [CommercialAccessoryController::class, 'update'])->name('update');
        });

        Route::get('/stats', [CommercialAuthController::class, 'viewStats'])->name('stats');
    });
});

Route::prefix('/magasins')->name('shops')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::post('/', [ShopController::class, 'select'])->name('select');

    Route::get('/disponibilite/{reference}', [AvailabilityController::class, 'show'])->name('availability.show');
});

require __DIR__.'/auth.php';
