<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReviewService
{
    /**
     * Create or update a review.
     */
    public function save(int $userId, int $productId, int $rating, ?string $review, bool $verifiedPurchase = false, bool $isApproved = false): Review
    {
        return DB::transaction(function () use ($userId, $productId, $rating, $review, $verifiedPurchase, $isApproved) {

            return Review::withTrashed()->updateOrCreate(
                ['user_id' => $userId, 'product_id' => $productId],
                [
                    'rating' => $rating,
                    'review' => $review,
                    'verified_purchase' => $verifiedPurchase,
                    'is_approved' => $isApproved,
                    'deleted_at' => null,
                ]
            );
        });
    }

    public function delete(int $userId, int $reviewId): bool
    {
        $review = Review::where('id', $reviewId)
            ->where('user_id', $userId)
            ->firstOrFail();

        return $review->delete();
    }

    public function getByProduct(int $productId)
    {
        return Review::with('user')
            ->where('product_id', $productId)
            ->where('is_approved', true) // only approved reviews visible
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
