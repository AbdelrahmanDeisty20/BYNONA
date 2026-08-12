<?php

namespace App\Service;

use App\Models\Category;

class CategoryService
{
    // جلب التصنيفات الرئيسية
    public function getRootCategories()
    {
        return Category::orderBy("sort_order", "ASC")->get()->makeHidden(["sort_order"]);
    }

    // جلب الأبناء لأي تصنيف
    public function getChildrenCategories(int $id)
    {
        return Category::where('parent_id', $id)->get();
    }
}

