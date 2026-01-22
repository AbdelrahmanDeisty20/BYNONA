<?php
namespace App\Service;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    protected int $cacheTime = 3600; // ثانية

    // جلب التصنيفات الرئيسية
    public function getRootCategories()
    {
        return Cache::remember(
            "categories_root_all",
            $this->cacheTime,
            fn () => Category::orderBy("sort_order","ASC")->get()->makeHidden(["sort_order"])
        );
    }

    // جلب الأبناء لأي تصنيف
    public function getChildrenCategories(int $id)
    {
        return Cache::remember(
            "category_{$id}_children_all",
            $this->cacheTime,
            fn () => Category::where('parent_id', $id)->get()
        );
    }
}
