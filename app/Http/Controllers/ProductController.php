<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductRecommendationResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\UserProductView;
use App\Services\ProductService;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Throwable;

class ProductController extends Controller
{
    public function __construct(protected ProductService $service) {}

    /* =========================
       PUBLIC PAGES
    ========================== */

    /** Public product listing */
    public function index(Request $request)
    {
        $userId = $request->user()?->id ?? null;
        return Inertia::render('Products/Index', [
            'products' => $this->service->publicList(
                $request->only('search', 'category_id'),
                $userId
            ),
            'categories' => Category::select('id', 'name')->get(),
            'filters' => $request->only('search', 'category_id'),
        ]);
    }

    /** Public product detail (slug) */
    public function showBySlug(string $slug, RecommendationService $recommendationService, Request $request)
    {
        $product = $this->service->findBySlug($slug);

        if ($request->user()) {
            UserProductView::updateOrCreate(
                [
                    'user_id' => $request->user()->id,
                    'product_id' => $product->id,
                ],
                ['viewed_at' => now()]
            );
        } else {
            $viewed = session('guest_viewed_products', []);
            if (! in_array($product->id, $viewed, true)) {
                $viewed[] = $product->id;
                session(['guest_viewed_products' => array_values(array_unique(array_slice($viewed, -30)))]);
            }
        }

        $similar = $recommendationService->getSimilarProducts($product, 8, $request->user());

        return Inertia::render('Products/Show', [
            'product' => $product,
            'initialReviews' => $product->reviews,
            'similarProducts' => ProductRecommendationResource::collection($similar)->resolve(),
        ]);
    }

    /* =========================
       ADMIN PAGES
    ========================== */

    /** Admin listing */
    public function adminIndex(Request $request)
    {
        return Inertia::render('Admin/Products/Index', [
            'products' => $this->service->adminList(
                $request->only('search', 'category_id')
            ),
            'categories' => Category::select('id', 'name')->get(),
        ]);
    }

    /** Admin detail */
    public function adminShow(int $id)
    {
        $products = $this->service->getLatestProducts(6);
        return Inertia::render('Admin/Products/Show', [
            'product' => $this->service->findById($id),
            'latestProducts' => $products,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        return Inertia::render('Admin/Products/Create', [
            'categories' => Category::select('id', 'name')->get(),
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);

        try {
            $this->service->create($request->validated());
            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors('Failed to create product');
        }
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product->load('category', 'images'),
            'categories' => Category::select('id', 'name')->get(),
        ]);
    }

    public function update(UpdateProductRequest $request, int $id)
    {
        $this->authorize('update', Product::findOrFail($id));
        Log::info('Product update request', [
            'user_id' => $request->user()->id,
            'product_id' => $id,
            'data' => $request->validated(),
        ]);
        try {
            $this->service->update($id, $request->validated());
            Log::info('Product updated successfully', [
                'user_id' => $request->user()->id,
                'product_id' => $id,
            ]);
            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully');
        } catch (Throwable $e) {
            Log::error('Failed to update product', [
                'user_id' => $request->user()->id,
                'product_id' => $id,
                'error' => $e->getMessage(),
            ]);
            report($e);
            return back()->withErrors('Failed to update product');
        }
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);

        $this->authorize('delete', $product);

        try {
            $this->service->delete($id);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product deleted');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors('Failed to delete product');
        }
    }
}
