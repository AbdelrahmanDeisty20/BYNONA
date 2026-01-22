<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\settings\{create, update};
use App\Models\{Setting,Governorate};

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::paginate(10);
        return view("dashboard.settings.index",compact("settings"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $governorates = Governorate::all();
        return view("dashboard.settings.create",compact("governorates"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(create $request)
    {
        $setting = Setting::create($request->validated());
        return response()->json([
            "success"=>true,
            "message"=>"created successfully"
        ]);
         
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $governorate = Setting::with("governorate")->findOrFail($id);
        return view("dashboard.settings.edit",compact("governorate"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(update $request, string $id)
    {
        $data = $request->validated();
        $setting= Setting::findOrFail($id);
        $setting->update($data);
        return response()->json([
            "success"=>true,
            "message"=>"updated successfully"
        ]);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $setting = Setting::findOrFail($id);
        $setting->delete();
        return redirect()->back();
    }
}