<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\products\{create, update};
use App\Models\{Brand, Category, Product, Property, User, AttributeDefinition};
use App\Notifications\ProductCreated;
use Illuminate\Support\Facades\{Cache, DB, Storage};

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Product::with('categories', 'brand');

        // لو فيه كلمة بحث
        if ($search = request('q')) {
            $query->where(function ($q) use ($search) {
                $q
                    ->where('name_en', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('desc_en', 'like', "%{$search}%")
                    ->orWhere('desc_ar', 'like', "%{$search}%")
                    // البحث في البراند
                    ->orWhereHas('brand', function ($bq) use ($search) {
                        $bq
                            ->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%");
                    })
                    // البحث في الأقسام
                    ->orWhereHas('categories', function ($cq) use ($search) {
                        $cq
                            ->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%");
                    });
            });
        }

        $products = $query->paginate(10)->withQueryString();  // withQueryString عشان يحتفظ بالكلمة عند التنقل بين الصفحات

        return view('dashboard.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $attributes = AttributeDefinition::with('values')->get();

        return view('dashboard.products.create', compact('categories', 'brands', 'attributes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(create $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $product = Product::create($data);

            if ($request->filled('category_id')) {
                $product->update(['category_id' => $request->category_id]);
            }

            if ($request->filled('brand_id')) {
                $product->brand()->associate($request->brand_id)->save();
            }

            // Save Variants (Properties)
            if ($request->has('variants')) {
                foreach ($request->variants as $variantData) {
                    $variantData['product_id'] = $product->id;
                    $variantData['name_en'] = $product->name_en;
                    $variantData['name_ar'] = $product->name_ar;

                    // Handle Images for variant (using first variant's images if provided at top level, or individual ones)
                    // For simplicity in unified flow, we'll allow images per variant
                    if (isset($variantData['main_image_file'])) {
                        $file = $variantData['main_image_file'];
                        $name = time() . '_main_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->storeAs('public/products', $name);
                        $variantData['main_image'] = $name;
                    }

                    // Handle Gallery Images (Multiple)
                    $galleryImages = [];
                    if (isset($variantData['gallery_files'])) {
                        foreach ($variantData['gallery_files'] as $file) {
                            $name = time() . '_gallery_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            $file->storeAs('public/products', $name);
                            $galleryImages[] = $name;
                        }
                    }
                    $variantData['images'] = $galleryImages;

                    $property = Property::create($variantData);

                    // Save Attributes
                    if (!empty($variantData['attributes'])) {
                        foreach ($variantData['attributes'] as $attr) {
                            $property->variantAttributes()->create($attr);
                        }
                    }
                }
            }

            DB::commit();
            Cache::flush();

            return response()->json([
                'status' => 'success',
                'message' => __('Product and variants created successfully!'),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['variants.variantAttributes', 'categories', 'brand'])->findOrFail($id);
        return view('dashboard.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $categories = Category::all();
        $brands = Brand::all();
        $product = Product::with(['variants.variantAttributes', 'categories'])->findOrFail($id);
        $attributes = AttributeDefinition::with('values')->get();

        // إظهار الحقول المخفية عشان الـ JS يشوفها (السعر والمواصفات)
        $product->variants->each(function ($variant) {
            $variant->makeVisible(['retail_price', 'wholesale_price']);
            $variant->variantAttributes->each->makeVisible(['key_en', 'key_ar', 'value_en', 'value_ar']);
        });

        return view('dashboard.products.edit', compact('categories', 'brands', 'product', 'attributes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Update $request, string $id)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);
            $data = $request->validated();

            $product->update($data);

            if ($request->filled('category_id')) {
                $product->update(['category_id' => $request->category_id]);
            }

            if ($request->filled('brand_id')) {
                $product->brand()->associate($request->brand_id)->save();
            }

            // Sync Variants (Properties)
            if ($request->has('variants')) {
                $incomingVariantIds = [];
                foreach ($request->variants as $variantData) {
                    $variantData['product_id'] = $product->id;
                    $variantData['name_en'] = $product->name_en;
                    $variantData['name_ar'] = $product->name_ar;

                    // Handle Images
                    if (isset($variantData['main_image_file'])) {
                        $file = $variantData['main_image_file'];
                        $name = time() . '_main_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->storeAs('public/products', $name);
                        $variantData['main_image'] = $name;
                    }

                    // Handle Gallery Images
                    if (isset($variantData['gallery_files'])) {
                        $galleryNames = [];
                        foreach ($variantData['gallery_files'] as $gFile) {
                            $gName = time() . '_gallery_' . uniqid() . '.' . $gFile->getClientOriginalExtension();
                            $gFile->storeAs('public/products', $gName);
                            $galleryNames[] = $gName;
                        }
                        // Replace or append logic (currently replacing for simplicity like main image)
                        $variantData['images'] = $galleryNames;
                    }

                    if (isset($variantData['id']) && !empty($variantData['id'])) {
                        $variant = Property::findOrFail($variantData['id']);
                        $variant->update($variantData);
                        $incomingVariantIds[] = $variant->id;
                    } else {
                        $variant = Property::create($variantData);
                        $incomingVariantIds[] = $variant->id;
                    }

                    // Sync Attributes
                    $variant->variantAttributes()->delete();
                    if (!empty($variantData['attributes'])) {
                        foreach ($variantData['attributes'] as $attr) {
                            $variant->variantAttributes()->create($attr);
                        }
                    }
                }

                // $product->variants()->whereNotIn('id', $incomingVariantIds)->delete();
            }

            DB::commit();
            Cache::flush();

            return response()->json([
                'status' => 'success',
                'message' => __('Product and variants updated successfully!'),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        Cache::flush();
        return redirect()->back()->with('success', __('Product deleted successfully!'));
    }
}
