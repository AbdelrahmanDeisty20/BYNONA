<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    public function index()
    {
        $governorates = Governorate::select(["name_ar","name_en","id"])->get();
        if ($governorates->isEmpty()) {
            return response()->json([
            "success"=>true,
            "message"=>"no governorates right now",
            "data"=>[]
            ]);
        }
        return response()->json([
            "success"=>true,
            "message"=>"recived governorates successfully",
            "data"=>$governorates
            ]);
        
    }
}
