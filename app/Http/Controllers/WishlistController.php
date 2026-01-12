<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishlistRequest;
use App\Models\Wishlist;
use App\Services\WishlistService;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Throwable;

class WishlistController extends Controller
{
    public function __construct(protected WishlistService $service) {}

    public function index(Request $request)
    {
        $items = $this->service->getAll($request->user()->id);
        return Inertia::render('Wishlist/Index', [
            'items' => $items,
        ]);
    }

    public function toggle(Request $request)
    {
        $userId = $request->user()->id;
        $productId = $request->input('product_id');

        $wishlistItem = Wishlist::withTrashed()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        try {
            if ($wishlistItem && !$wishlistItem->trashed()) {
                $this->service->remove($userId, $productId);
                $added = false;
            } else {
                $this->service->add($userId, $productId);
                $added = true;
            }

            return back()->with('success', $added ? 'Product added to wishlist' : 'Product removed from wishlist');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }
}
