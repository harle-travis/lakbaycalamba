<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Establishment;
use App\Models\Stamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function store(Request $request, $establishmentId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Check if user has a stamp for this establishment
            $userStamp = Stamp::where('user_id', Auth::id())
                ->where('establishment_id', $establishmentId)
                ->first();

            if (!$userStamp) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must visit this establishment and get a stamp before you can leave a review.'
                ], 422);
            }

            // Check if user already has a review for this establishment
            $existingReview = Review::where('user_id', Auth::id())
                ->where('establishment_id', $establishmentId)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this establishment.'
                ], 422);
            }

            // Create new review
            $review = Review::create([
                'user_id' => Auth::id(),
                'establishment_id' => $establishmentId,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            // Load the user relationship for the response
            $review->load('user');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review submitted successfully!',
                'review' => $review
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Review creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit review. Please try again.'
            ], 500);
        }
    }

    public function getEstablishmentReviews(Request $request, $establishmentId)
    {
        $establishment = Establishment::findOrFail($establishmentId);
        
        $query = $establishment->reviews()->with('user');

        // Filter by rating
        if ($request->filled('rating') && $request->rating !== 'all') {
            $query->where('rating', $request->rating);
        }

        // Sort by date (newest first by default)
        $sortBy = $request->get('sort', 'newest');
        if ($sortBy === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $reviews = $query->get();

        // Check if current user has already reviewed this establishment
        $userReview = null;
        if (Auth::check()) {
            $userReview = Review::where('user_id', Auth::id())
                ->where('establishment_id', $establishmentId)
                ->first();
        }

        return response()->json([
            'success' => true,
            'reviews' => $reviews,
            'userReview' => $userReview,
            'totalReviews' => $reviews->count(),
            'averageRating' => $reviews->avg('rating')
        ]);
    }

    public function checkUserReview($establishmentId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'hasReview' => false,
                'hasStamp' => false,
                'message' => 'Please log in to leave a review.'
            ]);
        }

        // Check if user has a stamp for this establishment
        $userStamp = Stamp::where('user_id', Auth::id())
            ->where('establishment_id', $establishmentId)
            ->first();

        $hasStamp = $userStamp ? true : false;

        // Check if user has a review for this establishment
        $review = Review::where('user_id', Auth::id())
            ->where('establishment_id', $establishmentId)
            ->first();

        return response()->json([
            'success' => true,
            'hasReview' => $review ? true : false,
            'hasStamp' => $hasStamp,
            'review' => $review,
            'canReview' => $hasStamp && !$review // Can review if has stamp but no existing review
        ]);
    }

    public function update(Request $request, $reviewId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        try {
            $review = Review::where('id', $reviewId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $review->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully!',
                'review' => $review->load('user')
            ]);

        } catch (\Exception $e) {
            \Log::error('Review update failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update review. Please try again.'
            ], 500);
        }
    }

    public function destroy($reviewId)
    {
        try {
            $review = Review::where('id', $reviewId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Review deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review. Please try again.'
            ], 500);
        }
    }
}