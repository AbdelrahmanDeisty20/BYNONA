<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\categories\Create;
use App\Http\Requests\Dashboard\categories\Update;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::orderBy('sort_order',"ASC")->paginate(10);

        return view('dashboard.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return view('dashboard.categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Create $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'_'.uniqid().'.'.$image->getClientOriginalExtension();
            $image->storeAs('public/categories', $imageName);
            $data['image'] = $imageName;
        }

        Category::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'],
            // 'parent_id' => $data['parent_id'],
            'image' => $data['image'],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Category created successfully!'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

        $category = Category::findOrFail($id);
        $categories = Category::where('id', '!=', $id)->get();

        return view('dashboard.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Update $request, string $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('image')) {

            if ($category->image && file_exists(storage_path('app/public/categories/'.$category->image))) {
                unlink(storage_path('app/public/categories/'.$category->image));
            }

            $file = $request->file('image');
            $imageName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $file->storeAs('public/categories', $imageName);

            $data['image'] = $imageName;
        }

        $category->update($data);

        return response()->json([
            'status' => true,
            'message' => __('Category updated successfully!'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        if ($category->image && file_exists(storage_path('app/public/categories/'.$category->image))) {
            unlink(storage_path('app/public/categories/'.$category->image));
        }

        $category->delete();

        return redirect()->back()->with('success', __('Category deleted successfully!'));
    }
}
