<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Throwable;

class ProductController extends Controller
{
    protected $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $products = $this->service->list(
            $request->only('search', 'category_id')
        );

        $products->getCollection()->transform(fn($product) => [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'slug' => $product->slug,
            'stock' => $product->stock,
            'is_active' => $product->is_active,
            'description' => $product->description,
            'category' => $product->category?->name,
            'discount_price' => 'Rs ' . $product->discount_price,
            'effective_price' => 'Rs ' . $product->effective_price,
            'discount_percentage' => $product->discount_percentage . '%',
            'primary_image' => optional(
                $product->images->firstWhere('is_primary', true)
            ) ? Storage::url($product->images->firstWhere('is_primary', true)->image_path) : null,
            'attributes' => $product->attributes ?? [],
        ]);

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => Category::select('id', 'name')->get(),
            'filters' => $request->only('search', 'category_id'),
        ]);
    }


    public function show(int $id)
    {
        $product = $this->service->findOrFail($id);

        return Inertia::render('Products/Show', [
            'product' => $product,
            'initialReviews' => $product->reviews,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        return Inertia::render('Products/Create', [
            'categories' => Category::select('id', 'name')->get(),
        ]);
    }


    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);

        try {
            $this->service->create($request->validated());
            return redirect()->route('products.index')
                ->with('success', 'Product created successfully');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors('Failed to create product');
        }
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        return Inertia::render('Products/Edit', [
            'product' => $product->load('category', 'images'),
            'categories' => Category::select('id', 'name')->get(),
        ]);
    }


    public function update(UpdateProductRequest $request, $id)
    {
        $this->authorize('update', Product::class);

        try {
            $this->service->update($id, $request->validated());
            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors('Failed to update product');
        }
    }

    public function destroy($id)
    {
        $this->authorize('delete', Product::class);

        try {
            $this->service->delete($id);
            return redirect()->route('products.index')
                ->with('success', 'Product deleted');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors('Failed to delete product');
        }
    }
}
