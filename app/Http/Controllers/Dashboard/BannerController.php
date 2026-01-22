<?php

namespace App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\{Cache, DB, Storage};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\banners\{create, update};
use App\Models\Banner;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::paginate(10);
        return view("dashboard.banners.index",compact("banners"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(create $request)
    {
        //
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
        $banner = Banner::findOrFail($id);
        return view("dashboard.banners.edit",compact("banner"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(update $request, string $id)
    {
        $banner = Banner::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {

            if ($banner->image && file_exists(storage_path('app/public/banners/'.$banner->image))) {
                unlink(storage_path('app/public/banners/'.$banner->image));
            }

            $file = $request->file('image');
            $imageName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/banners', $imageName);

            $data['image'] = $imageName;
        }

        $banner->update($data);
                            Cache::flush();


        return response()->json([
            'status' => true,
            'message' => 'banner updated successfully',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}