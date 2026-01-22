<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\bannersProduct\{create,update};
use App\Models\ProductBanner;
use Illuminate\Http\Request;

class ProductBannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productBanners = ProductBanner::paginate(10);
        return view("dashboard.bannersProduct.index",compact("productBanners"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("dashboard.bannersProduct.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(create $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->storeAs('public/advertisements', $imageName);
            $data['image'] = $imageName;
        }

        ProductBanner::create([
            'title_ar' => $data['title_ar'],
            'title_en' => $data['title_en'],
            'desc_en' => $data['desc_en'],
            'desc_ar' => $data['desc_ar'],
            'price' => $data['price'],
            'image' => $data['image'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'created banner successfully',
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
        $banner = ProductBanner::findOrFail($id);
        return view("dashboard.bannersProduct.edit",compact("banner"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(update $request, string $id)
    {
        $banner = ProductBanner::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {

            if ($banner->image && file_exists(storage_path('app/public/advertisements/'.$banner->image))) {
                unlink(storage_path('app/public/advertisements/'.$banner->image));
            }

            $file = $request->file('image');
            $imageName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/advertisements', $imageName);

            $data['image'] = $imageName;
        }

        $banner->update($data);

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
        $banner = ProductBanner::findOrFail($id);

        if ($banner->image && file_exists(storage_path('app/public/advertisements/'.$banner->image))) {
            unlink(storage_path('app/public/advertisements/'.$banner->image));
        }

        $banner->delete();

        return redirect()->back();
    }
}
