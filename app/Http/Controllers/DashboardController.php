<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductRecommendationResource;
use App\Models\UserProductView;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request, RecommendationService $recommendations): Response
    {
        $user = $request->user();
        $profile = $user->getOrCreateProfile();

        $recentlyViewed = UserProductView::query()
            ->where('user_id', $user->id)
            ->with(['product.category', 'product.images', 'product.reviews'])
            ->orderByDesc('viewed_at')
            ->limit(8)
            ->get()
            ->pluck('product')
            ->filter()
            ->values();

        $recommended = $recommendations->getPersonalizedRecommendations($user, 8);

        $wishlist = $user->wishlist()->with(['product.category', 'product.images'])->latest()->limit(8)->get();

        $reviews = $user->reviews()
            ->with(['product:id,name,slug', 'aspectSentiments'])
            ->latest()
            ->limit(10)
            ->get();

        $timeline = UserProductView::query()
            ->where('user_id', $user->id)
            ->with(['product:id,name,slug'])
            ->orderByDesc('viewed_at')
            ->limit(12)
            ->get()
            ->map(fn (UserProductView $v) => [
                'type' => 'view',
                'label' => 'Viewed '.$v->product?->name,
                'at' => $v->viewed_at?->toIso8601String(),
            ]);

        return Inertia::render('Dashboard', [
            'recentlyViewed' => ProductRecommendationResource::collection($recentlyViewed)->resolve(),
            'recommendedForYou' => ProductRecommendationResource::collection($recommended)->resolve(),
            'wishlist' => $wishlist->map(fn ($w) => [
                'id' => $w->product_id,
                'product' => [
                    'id' => $w->product->id,
                    'name' => $w->product->name,
                    'slug' => $w->product->slug,
                    'main_image' => $w->product->images->first()?->url ?? '/img/default.png',
                    'effective_price' => (float) $w->product->effective_price,
                ],
            ]),
            'yourReviews' => $reviews->map(fn ($r) => [
                'id' => $r->id,
                'rating' => $r->rating,
                'text' => $r->review,
                'is_approved' => $r->is_approved,
                'aspects' => $r->aspectSentiments->map(fn ($a) => [
                    'aspect' => $a->aspect,
                    'sentiment' => $a->sentiment,
                ]),
                'product' => $r->product?->only(['id', 'name', 'slug']),
            ]),
            'interestProfile' => [
                'preferred_categories' => $profile->preferred_categories ?? [],
                'preference_score' => $profile->preference_score,
                'last_interest_update' => $profile->last_interest_update?->toIso8601String(),
            ],
            'activityTimeline' => $timeline,
        ]);
    }
}
