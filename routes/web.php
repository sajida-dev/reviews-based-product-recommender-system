<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\Settings\SocialLoginController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\ForceTwoFactorSetup;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');



Route::get('auth/google', [SocialLoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('auth/facebook', [SocialLoginController::class, 'redirectToFacebook']);
Route::get('auth/facebook/callback', [SocialLoginController::class, 'handleFacebookCallback']);

Route::middleware([
    'auth',
    'verified',
    ForceTwoFactorSetup::class,
])->group(function () {

    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create'); // admin
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store'); // admin
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit'); // admin
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update'); // admin
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy'); // admin

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store'); // admin
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update'); // admin
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy'); // admin


    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{itemId}', [CartController::class, 'updateQty'])->name('cart.updateQty');
    Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');


    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');


    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');


    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});



require __DIR__ . '/settings.php';
