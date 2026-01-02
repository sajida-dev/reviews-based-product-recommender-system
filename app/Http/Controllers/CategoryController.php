<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    protected $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return Inertia::render('Admin/Categories/Index', [
            'categories' => $this->service->list(),
            'parentOptions' => $this->service->parentOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
        ]);

        if (!$this->service->create($request->only([
            'name',
            'slug',
            'parent_id',
            'description',
        ]))) {
            return back()->withErrors('Unable to create category');
        }

        return redirect()->route('categories.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id', 'not_in:' . $id],
            'description' => ['nullable', 'string'],
        ]);

        if (!$this->service->update($id, $request->only([
            'name',
            'slug',
            'parent_id',
            'description',
        ]))) {
            return back()->withErrors('Unable to update category');
        }

        return redirect()->route('categories.index');
    }

    public function destroy($id)
    {
        if (!$this->service->delete($id)) return back()->withErrors('Unable to delete category');
        return redirect()->route('categories.index');
    }
}
