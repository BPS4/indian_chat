<?php

namespace App\Http\Controllers\Api\Review;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function add_review(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'hotel_id'   => 'required|exists:hotels,id',
            'booking_id' => 'nullable|exists:booking,id', // ✅ corrected table name
            'rating'     => 'required|integer|min:1|max:5',
            'review'     => 'nullable|string|max:1000',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // max 2MB
        ]);

        if ($validator->fails()) {
            // return only the first validation error
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $userId = Auth::id(); // fallback for manual user_id in testing
        if (!$userId) {
            return response()->json([
                'message' => 'User not authenticated.',
            ], 401);
        }
        // Prevent duplicate review for same booking
        $exists = Review::where('user_id', $userId)
            ->where('booking_id', $request->booking_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'You have already submitted a review for this booking.',
            ], 409);
        }

        // Optional: ensure booking belongs to this user
        if ($request->booking_id) {
            $booking = Booking::find($request->booking_id);

            if ($booking && $booking->user_id != $userId) {
                return response()->json([
                    'message' => 'You are not authorized to review this booking.',
                ], 403);
            }
        }

        // Handle image upload (if provided)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/review'), $imageName);
            $imagePath = 'images/review/' . $imageName;
        }
        // Create the review
        $review = Review::create([
            'user_id'     => $userId,
            'hotel_id'    => $request->hotel_id,
            'booking_id'  => $request->booking_id,
            'rating'      => $request->rating,
            'review'      => $request->review,
            'image'       => $imagePath,
            'is_approved' => false, // default, can be auto-approved if needed
        ]);

        return response()->json([
            'message' => 'Review submitted successfully!',
            'data' => $review,
        ], 201);
    }



    public function all_reviews($hotel_id)
    {
        // Fetch all approved reviews with user details
        $reviews = Review::with('user:id,name', 'hotel:id,name') // limit user fields to avoid large payload
            ->where('hotel_id', $hotel_id)
            ->where('is_approved', true)
            ->latest()
            ->get();

        // Calculate average rating
        $averageRating = Review::where('hotel_id', $hotel_id)
            ->where('is_approved', true)
            ->avg('rating');

        // Handle empty result
        if ($reviews->isEmpty()) {
            return response()->json([
                'message' => 'No reviews found for this hotel.',
                'data' => [],
                'average_rating' => 0,
                'total_reviews' => 0,
                'review_analytics' => null
            ], 200);
        }

        // Review analytics: count per star rating (1-5)
        $reviewSummary = Review::select('rating', DB::raw('count(*) as total'))
            ->where('hotel_id', $hotel_id)
            ->where('is_approved', true)
            ->groupBy('rating')
            ->orderBy('rating', 'asc')
            ->get();

        // Ensure all ratings 1-5 are present, even if 0
        $reviewAnalytics = collect([1, 2, 3, 4, 5])->mapWithKeys(function ($star) use ($reviewSummary) {
            $match = $reviewSummary->firstWhere('rating', $star);
            return [$star => $match ? $match->total : 0];
        });

        return response()->json([
            'message' => 'All approved reviews',
            'average_rating' => round($averageRating, 1),
            'total_reviews' => $reviews->count(),
            'review_analytics' => $reviewAnalytics,
            'data' => $reviews
        ], 200);
    }
}
