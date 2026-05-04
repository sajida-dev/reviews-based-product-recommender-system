<?php

use App\Http\Controllers\Api\ProductSimilarController;
use App\Http\Controllers\Api\RecommendationApiController;
use App\Http\Controllers\Api\SearchApiController;
use Illuminate\Support\Facades\Route;

Route::get('/products/{product}/similar', [ProductSimilarController::class, '__invoke'])
    ->name('products.similar');

Route::get('/search', [SearchApiController::class, '__invoke'])->name('search');

Route::get('/recommendations', [RecommendationApiController::class, '__invoke'])->name('recommendations.index');
