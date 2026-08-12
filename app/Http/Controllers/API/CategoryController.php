<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy("sort_order", "ASC")->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'image_path' => $category->image_path,
                'parent_id' => $category->parent_id,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => __('Categories received successfully'),
            'data' => $categories,
        ]);
    }

    public function subCategory(Request $request, $id)
    {
        return $this->subTree($request, $id);
    }

    public function subTree(Request $request, $id)
    {
        $children = Category::where('parent_id', $id)->get();

        if ($children->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => __('This category does not exist or has no children'),
                'data' => [],
            ], 404);
        }

        $mappedChildren = $children->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'image_path' => $category->image_path,
                'parent_id' => $category->parent_id,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => __('Children categories retrieved successfully'),
            'data' => $mappedChildren,
        ]);
    }
}