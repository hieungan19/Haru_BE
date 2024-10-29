<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function getReviews(Request $request)
    {
        $data = $request->input();
        $getReviews = Review::where('product_id', $data['productID'])->get();
        return $getReviews;
    }
    public function addNewReview(Request $request)
    {
        $data = $request->input();
        
        $review = Review::create([
            'product_id' => $data['productID'],
            'user_id' => $data['userID'],
            'text' => $data['text'],
            'stars' => $data['stars'],
            'date' => $data['date']
        ]);
        $review->save();

        return $review;
    }
}
