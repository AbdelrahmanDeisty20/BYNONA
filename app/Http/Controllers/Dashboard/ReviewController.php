<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with("user")->paginate(10);
        return view("dashboard.reviews.index",compact("reviews"));
    }
    public function destroy($id)
    {
        $reviews = Review::findOrFail($id);
        $reviews->delete();
        return redirect()->back();
    }
    
}