<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\brands\{create, edit};
use App\Models\Brand;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::paginate(10);
        return view("dashboard.brands.index",compact("brands"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("dashboard.brands.create");
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
            $image->storeAs('public/brands', $imageName);
            $data['image'] = $imageName;
        }

        Brand::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            'image' => $data['image'],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Brand created successfully!'),
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
        $brand = Brand::findOrFail($id);
        return view("dashboard.brands.edit",compact("brand"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(edit $request, string $id)
    {
        $brand = Brand::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {

            if ($brand->image && file_exists(storage_path('app/public/brands/'.$brand->image))) {
                unlink(storage_path('app/public/brands/'.$brand->image));
            }

            $file = $request->file('image');
            $imageName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/brands', $imageName);

            $data['image'] = $imageName;
        }

        $brand->update($data);

        return response()->json([
            'status' => true,
            'message' => __('Brand updated successfully!'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
         $brand = Brand::findOrFail($id);

        if ($brand->image && file_exists(storage_path('app/public/brands/'.$brand->image))) {
            unlink(storage_path('app/public/brands/'.$brand->image));
        }

        $brand->delete();

        return redirect()->back()->with('success', __('Brand deleted successfully!'));
    }
}