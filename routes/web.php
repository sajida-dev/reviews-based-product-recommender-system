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
use App\Services\ProductService;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canRegister' => Features::enabled(Features::registration()),
//     ]);
// })->name('home');



// Route::get('auth/google', [SocialLoginController::class, 'redirectToGoogle'])->name('auth.google');
// Route::get('auth/google/callback', [SocialLoginController::class, 'handleGoogleCallback'])->name('auth.google.callback');
// Route::get('auth/facebook', [SocialLoginController::class, 'redirectToFacebook']);
// Route::get('auth/facebook/callback', [SocialLoginController::class, 'handleFacebookCallback']);

// Route::middleware([
//     'auth',
//     'verified',
//     ForceTwoFactorSetup::class,
// ])->group(function () {

//     Route::get('dashboard', function () {
//         return Inertia::render('Dashboard');
//     })->name('dashboard');

//     Route::get('/products', [ProductController::class, 'index'])->name('products.index');
//     Route::get('/products/create', [ProductController::class, 'create'])->name('products.create'); // admin
//     Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
//     Route::post('/products', [ProductController::class, 'store'])->name('products.store'); // admin
//     Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit'); // admin
//     Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update'); // admin
//     Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy'); // admin

//     Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
//     Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store'); // admin
//     Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update'); // admin
//     Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy'); // admin


//     Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
//     Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
//     Route::put('/cart/update/{itemId}', [CartController::class, 'updateQty'])->name('cart.updateQty');
//     Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');


//     Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
//     Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');


//     Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');


//     Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
//     Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
// });


// Home page
Route::get('/', function (ProductService $productService) {
    $products = $productService->getLatestProducts(6);
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
        'products' => $products
    ]);
})->name('home');

// Public product listing
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Product detail page (SEO-friendly slug)
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
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{itemId}', [CartController::class, 'updateQty'])->name('cart.updateQty');
    Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout & Orders
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});


Route::middleware(['auth', 'verified', ForceTwoFactorSetup::class])->group(function () {

    // Admin product listing (dashboard)
    Route::get('/admin/products', [ProductController::class, 'adminIndex'])->name('admin.products.index');

    // Admin product CRUD
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/admin/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
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
