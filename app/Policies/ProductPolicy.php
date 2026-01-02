<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * View any products
     */
    public function viewAny(User $user): bool
    {
        return true; // public listing allowed
    }

    /**
     * View a single product
     */
    public function view(User $user, Product $product): bool
    {
        return true;
    }

    /**
     * Create product
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Update product
     */
    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }

    /**
     * Delete product (soft delete)
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin();
    }

    /**
     * Restore product
     */
    public function restore(User $user, Product $product): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Force delete (rare)
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return false; // safety-first
    }
}
