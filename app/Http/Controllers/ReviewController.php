<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Services\ReviewService;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Throwable;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $service) {}

    public function store(ReviewRequest $request)
    {
        try {
            $review = $this->service->save(
                $request->user()->id,
                $request->product_id,
                $request->rating,
                $request->review,
                true,
                false
            );
            $review->load('user');

            return redirect()->back()->with([
                'newReview' => $review
            ]);
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }

    public function destroy(Request $request, int $reviewId)
    {
        try {
            $this->service->delete($request->user()->id, $reviewId);
            return back()->with('success', 'Review deleted successfully.');
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors($e->getMessage());
        }
    }

    public function index(Request $request, int $productId)
    {
        $reviews = $this->service->getByProduct($productId);
        return Inertia::render('Reviews/Index', [
            'reviews' => $reviews,
        ]);
    }
}
