<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Service\CategoryService;
use Illuminate\Support\Facades\Request;

class CategoryController extends Controller
{
    protected CategoryService $service;

    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $categories = $this->service->getRootCategories($request->page ?? 1);

        return response()->json([
            'success' => true,
            'message' => __('Categories received successfully'),
            'data' => $categories,
        ]);
    }

    public function subTree(Request $request, $id)
    {
        $children = $this->service->getChildrenCategories($id, $request->page ?? 1);

        if ($children->isEmpty()) {
            return response()->json([
                'error' => true,
                'message' => __('This category does not exist or has no children'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => __('Children categories retrieved successfully'),
            'data' => $children,
        ]);
    }
}