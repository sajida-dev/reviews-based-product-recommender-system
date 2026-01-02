<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishlistRequest;
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

    public function store(WishlistRequest $request)
    {
        try {
            $this->service->add($request->user()->id, $request->product_id);
            return back()->with('success', 'Product added to wishlist');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }

    public function destroy(Request $request, int $productId)
    {
        try {
            $this->service->remove($request->user()->id, $productId);
            return back()->with('success', 'Product removed from wishlist');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }
}
