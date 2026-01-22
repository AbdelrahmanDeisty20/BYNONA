<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\reviewRequest;
use App\Http\Requests\API\reviews\create;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
public function index(reviewRequest $request)
{
             $priceMode = app('price_mode');
    $product = $request -> validated();
    $reviews = Review::with("user")
        ->where("product_id", $product['product_id'])
        ->where("type",$priceMode)
        
        ->paginate(10);

    if ($reviews->isEmpty()) {
        return response()->json([
            "success" => true,
            "message" => 'no reviews',
            "data" => []
        ]);
    }

    return response()->json([
        "success" => true,
        "message" => 'retrieved reviews successfully',
        "data" => $reviews->makeHidden("type")
    ]);
}
    public function store(create $request)
    {
         $priceMode = app('price_mode');
        $data = $request->validated();
        $user = Auth::user();
        $reviews = Review::create([
            "comment"=>$data["comment"],
            "rate"=>$data["rate"],
            "user_id"=>$user->id,
            "product_id"=>$data["product_id"],
        'type'       => $priceMode,
        ]);
        return response()->json([
            "success"=>true,
            "message"=> __("added reviews succesfully"),
            "data"=>$reviews
        ]);
    }
}