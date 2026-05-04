<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductRecommendationResource;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserProductView;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request, RecommendationService $recommendations): Response|RedirectResponse
    {
        $user = $request->user();
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        $profile = $user->getOrCreateProfile();

        $ordersQuery = Order::query()->where('user_id', $user->id);
        $totalOrders = (clone $ordersQuery)->count();
        $completedOrders = (clone $ordersQuery)
            ->whereIn('status', ['processing', 'shipped', 'delivered'])
            ->count();
        $cancelledOrders = (clone $ordersQuery)->where('status', 'cancelled')->count();
        $totalSpend = (float) ((clone $ordersQuery)
            ->whereIn('status', ['processing', 'shipped', 'delivered'])
            ->sum('total'));
        $avgOrderValue = $completedOrders > 0 ? round($totalSpend / $completedOrders, 2) : 0.0;
        $itemsPurchased = (int) OrderItem::query()
            ->whereHas('order', fn ($q) => $q->where('user_id', $user->id))
            ->sum('quantity');
        $wishlistCount = (int) $user->wishlist()->count();
        $reviewCount = (int) $user->reviews()->count();

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

        $recentOrders = $ordersQuery
            ->latest()
            ->limit(5)
            ->get(['id', 'order_number', 'total', 'status', 'created_at'])
            ->map(fn (Order $o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'total' => (float) $o->total,
                'status' => $o->status,
                'created_at' => $o->created_at?->toIso8601String(),
            ]);

        return Inertia::render('Dashboard', [
            'insights' => [
                'total_spend' => round($totalSpend, 2),
                'total_orders' => $totalOrders,
                'completed_orders' => $completedOrders,
                'cancelled_orders' => $cancelledOrders,
                'avg_order_value' => $avgOrderValue,
                'items_purchased' => $itemsPurchased,
                'wishlist_count' => $wishlistCount,
                'review_count' => $reviewCount,
            ],
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
            'recentOrders' => $recentOrders,
        ]);
    }
}
