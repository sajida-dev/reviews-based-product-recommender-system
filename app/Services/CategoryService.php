<?php

namespace App\Services;

use App\Models\Category;
use Exception;

class CategoryService
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}
    public function list()
    {
        return Category::with('parent')
            ->orderBy('name')
            ->get()
            ->map(fn($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'parent' => $category->parent
                    ? [
                        'id' => $category->parent->id,
                        'name' => $category->parent->name,
                    ]
                    : null,
            ]);
    }

    public function parentOptions()
    {
        return Category::orderBy('name')
            ->get()
            ->map(fn($category) => [
                'label' => $category->name,
                'value' => $category->id,
            ]);
    }

    public function create(array $data): ?Category
    {
        try {
            return Category::create($data);
        } catch (Exception $e) {
            report($e);
            return null;
        }
    }

    public function update(int $id, array $data): ?Category
    {
        $category = Category::find($id);
        if (!$category) return null;

        try {
            $category->update($data);
            return $category;
        } catch (Exception $e) {
            report($e);
            return null;
        }
    }

    public function delete(int $id): bool
    {
        $category = Category::find($id);
        if (!$category) return false;

        return $category->safeDelete();
    }
}
