<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = Review::latest()->get();

        return ReviewResource::collection($reviews);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReviewRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        $existingReview = Review::where('user_id', $data['user_id'])->where('product_id', $data['product_id'])->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You have already left a review for this product.'
            ], 422);
        }

        // Jika belum, buat review baru
        $review = Review::create($data);

        return new ReviewResource($review);
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        return new ReviewResource($review);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReviewRequest $request, Review $review)
    {
        $data = $request->validated();

        $review->update($data);

        return new ReviewResource($review);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json(['message' => 'Review deleted.']);
    }
}
