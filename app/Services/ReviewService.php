<?php

namespace App\Services;

use App\Models\Review;
use App\Services\Moderation\SpamCheckService;
use App\Services\Reviews\ReviewPreprocessor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewService
{
    protected ReviewPreprocessor $preprocessor;
    protected SpamCheckService $spamChecker;

    public function __construct(
        ReviewPreprocessor $preprocessor,
        SpamCheckService $spamChecker
    ) {
        $this->preprocessor = $preprocessor;
        $this->spamChecker = $spamChecker;
    }

    /**
     * Create or update a review.
     */
    public function save(
        int $userId,
        int $productId,
        int $rating,
        ?string $review,
        bool $verifiedPurchase = false,
        bool $isApproved = false
    ): Review {
        return DB::transaction(function () use ($userId, $productId, $rating, $review, $verifiedPurchase, $isApproved) {
            $rawReview = is_string($review) ? trim($review) : null;

            // Preprocess text
            $preprocessed = $rawReview !== null && $rawReview !== ''
                ? $this->preprocessor->preprocess($rawReview)
                : null;

            // Spam/toxicity check
            $spamResult = ['flagged' => false];
            if ($preprocessed !== null && $preprocessed !== '') {
                try {
                    $spamResult = $this->spamChecker->check($preprocessed);
                } catch (\Throwable $e) {
                    // Do not block checkout-critical user flow if moderation is temporarily down.
                    Log::warning('Review moderation unavailable; saving review without spam flag.', [
                        'user_id' => $userId,
                        'product_id' => $productId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // If flagged, mark as not approved
            $isApprovedFinal = $isApproved && !$spamResult['flagged'];
            Log::info("Review moderation result", [
                'user_id' => $userId,
                'product_id' => $productId,
                'spam_flagged' => $spamResult['flagged'] ?? false,
                'is_approved_final' => $isApprovedFinal,
            ]);
            // Save review with raw and preprocessed text
            return Review::withTrashed()->updateOrCreate(
                ['user_id' => $userId, 'product_id' => $productId],
                [
                    'rating' => $rating,
                    'review' => $preprocessed, // store preprocessed for ML pipelines
                    'raw_text' => $rawReview,  // store original
                    'verified_purchase' => $verifiedPurchase,
                    'is_approved' => $isApprovedFinal,
                    'spam_flagged' => $spamResult['flagged'] ?? false,
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
