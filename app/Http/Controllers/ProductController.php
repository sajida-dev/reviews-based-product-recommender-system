<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
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
    public function showBySlug(string $slug)
    {
        $product = $this->service->findBySlug($slug);

        return Inertia::render('Products/Show', [
            'product' => $product,
            'initialReviews' => $product->reviews,
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
        $this->authorize('update', Product::class);

        try {
            $this->service->update($id, $request->validated());
            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors('Failed to update product');
        }
    }

    public function destroy(int $id)
    {
        $this->authorize('delete', Product::class);

        try {
            $this->service->delete($id);
            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors('Failed to delete product');
        }
    }
}
