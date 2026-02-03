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
use App\Http\Middleware\AdminMiddleware;
use App\Services\ProductService;
use Illuminate\Support\Facades\Auth;



// Home page
Route::get('/', function (ProductService $productService) {
    $userId = Auth::id();

    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
        'products' => $productService->getLatestProducts(6, $userId),
        'adProducts' => $productService->getHomeAdProducts(2),
    ]);
})->name('home');

Route::get('/features', function () {
        return Inertia::render('Feature');
    })->name('feature');

Route::get('/about',function () {
        return Inertia::render('About');
    })->name('about');

    Route::get('/contact',function () {
        return Inertia::render('Contact');
    })->name('contact');

// Public product listing
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Product detail page 
Route::get('/products/{slug}', [ProductController::class, 'showBySlug'])->name('products.show');

// Social Login
Route::get('auth/google', [SocialLoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('auth/facebook', [SocialLoginController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('auth/facebook/callback', [SocialLoginController::class, 'handleFacebookCallback'])->name('auth.facebook.callback');


Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard for all users
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/{productId}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::put('/cart/update/{itemId}', [CartController::class, 'updateQty'])->name('cart.updateQty');


    // Checkout & Orders
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    
    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});


Route::middleware(['auth', 'verified', ForceTwoFactorSetup::class, AdminMiddleware::class])->group(function () {

    // Admin product listing (dashboard)
    Route::get('/admin/products', [ProductController::class, 'adminIndex'])->name('admin.products.index');

    // Admin product CRUD
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/admin/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/admin/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');

    // Admin product detail view
    Route::get('/admin/products/{id}', [ProductController::class, 'adminShow'])->name('admin.products.show');

    // Admin category management
    Route::get('/admin/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/admin/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/admin/categories/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/admin/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
});


require __DIR__ . '/settings.php';
