<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\{AddressRequest, AddressUpdateRequest};
use App\Models\Address;
use Illuminate\Support\Facades\{Auth, Request};

class AddressController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $addresses = Address::where('user_id', $user->id)->paginate(10);

        return response()->json([
            'message' => __('recived address successfully'),
            'data' => $addresses,
        ]);
        
    }

    public function store(AddressRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        $data['user_id'] = $user->id;
        $address = Address::create($data);

        return response()->json([
            'success' => true,
            'message' => __('create address successfuly'),
            'data' => $address,
        ]);
    }
    public function update(AddressUpdateRequest $request)
    {
        $auth = Auth::user();
        $data = $request->validated();
        $address = Address::where("user_id",$auth->id)->findOrFail($data['address_id']);
        $address->update($data);
        return response()->json([
            "success"=>true,
            "message"=>"address updated successfully"
        ]);
    }
    public function destroy(Request $request,$id)
    {
        $address = Address::find($id);
        if(!$address)
        {
            return response()->json([
                "success"=>true,
                "message"=>'there is no address with this id'
            ]);
        }
        $address->delete();
        return response()->json([
            "sucess"=>true,
            "message"=>'deleted successfully' 
        ]);
    }
}