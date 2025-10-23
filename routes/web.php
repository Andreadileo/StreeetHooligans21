<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Struttura:
| - Home + Catalogo
| - Product show via slug (model binding {product:slug})
| - Carrello basato su variant_id
| - Checkout (finto pagamento) protetto da auth
|--------------------------------------------------------------------------
*/

/**
 * HOME + CATALOGO
 * Se non usi HomeController, punta pure l'home a CatalogController@index.
 */
Route::get('/',                [HomeController::class,    'index'])->name('home');
Route::get('/catalog',         [CatalogController::class, 'index'])->name('catalog.index');

/**
 * PRODUCT SHOW (via slug)
 * Esempio URL: /products/brushed-bsp-dark-navy
 * Assicurati che CatalogController@show abbia la firma: show(\App\Models\Product $product)
 */
Route::get('/products/{product:slug}', [CatalogController::class, 'show'])
    ->name('product.show');

/**
 * CARRELLO (session-based, varianti)
 * - ADD: nessun parametro in URL → nel body inviamo variant_id + qty
 * - UPDATE/REMOVE: operano su {variant} (ProductVariant route-model binding)
 */
Route::get   ('/cart',                      [CartController::class, 'index'])->name('cart.index');
Route::post  ('/cart/add',                  [CartController::class, 'add'])->name('cart.add');
Route::patch ('/cart/update/{variant}',     [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{variant}',     [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear',                [CartController::class, 'clear'])->name('cart.clear');

/**
 * CHECKOUT (ora accessibile anche agli ospiti, l'ordine è legato all'utente se autenticato)
 */
Route::get ('/checkout',          [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout',          [CheckoutController::class, 'process'])->name('checkout.process');
Route::get ('/checkout/success',  [CheckoutController::class, 'success'])->name('checkout.success');
Route::get ('/checkout/cancel',   [CheckoutController::class, 'cancel'])->name('checkout.cancel');

/**
 * AREA ADMIN (gestione catalogo)
 */
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('products', AdminProductController::class)->except(['show']);
    });

// Esempio dashboard protetta (opzionale)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))
        ->middleware('verified')
        ->name('dashboard');

    // Profilo (se usi Fortify/Jetstream con file dedicato)
    require __DIR__.'/profile.php';
});

// Auth scaffolding (login/registrazione/forgot), se presente nel progetto
require __DIR__.'/auth.php';
