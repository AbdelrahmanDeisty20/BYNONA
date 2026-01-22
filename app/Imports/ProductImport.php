<?php

namespace App\Imports;

use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Property;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToCollection, WithHeadingRow
{
    private $lastProperty = null;
    private $lastPropertyConfig = null;
    private $downloadCache = [];
    private $extractedImagesPath = null;

    public function __construct($extractedImagesPath = null)
    {
        $this->extractedImagesPath = $extractedImagesPath;
    }

    public function collection(Collection $rows)
    {
        // Reset cache for each import session
        $this->downloadCache = [];
        foreach ($rows as $index => $row) {
            // 0. Clean string inputs (Important for matching)
            $row = $row->map(function ($value) {
                return is_string($value) ? trim($value) : $value;
            });

            // Skip completely empty rows
            if (empty($row['product_name_ar']) && empty($row['product_name_en'])) {
                // Check if it's a "continuation row" for attributes
                if ($this->lastProperty && (!empty($row['key_ar']) || !empty($row['key_en']))) {
                    $this->importSeparatedAttributes($this->lastProperty->id, $row);
                }
                continue;
            }

            // 1. Find or Create Category (Case-Insensitive)
            $category = null;
            if (!empty($row['category_name_ar']) || !empty($row['category_name_en'])) {
                $nameAr = $row['category_name_ar'] ?? $row['category_name_en'];
                $nameEn = $row['category_name_en'] ?? $row['category_name_ar'];

                $category = Category::whereRaw('LOWER(name_ar) = ?', [strtolower($nameAr)])
                    ->orWhereRaw('LOWER(name_en) = ?', [strtolower($nameEn)])
                    ->first();

                if (!$category) {
                    $category = Category::create([
                        'name_ar' => $nameAr,
                        'name_en' => $nameEn,
                        'parent_id' => 0,
                        'sort_order' => 0
                    ]);
                }
            }

            // 2. Find or Create Brand (Case-Insensitive)
            $brand = null;
            if (!empty($row['brand_name_ar']) || !empty($row['brand_name_en'])) {
                $nameAr = $row['brand_name_ar'] ?? $row['brand_name_en'];
                $nameEn = $row['brand_name_en'] ?? $row['brand_name_ar'];

                $brand = Brand::whereRaw('LOWER(name_ar) = ?', [strtolower($nameAr)])
                    ->orWhereRaw('LOWER(name_en) = ?', [strtolower($nameEn)])
                    ->first();

                if (!$brand) {
                    $brand = Brand::create([
                        'name_ar' => $nameAr,
                        'name_en' => $nameEn
                    ]);
                }
            }

            // 3. Find or Create Product (Matched by Name AND Type)
            $productNameAr = $row['product_name_ar'];
            $productNameEn = $row['product_name_en'];
            $type = strtolower($row['type'] ?? 'retail');

            $product = Product::where('type', $type)
                ->where(function ($q) use ($productNameAr, $productNameEn) {
                    if ($productNameAr)
                        $q->whereRaw('LOWER(name_ar) = ?', [strtolower($productNameAr)]);
                    if ($productNameEn)
                        $q->orWhereRaw('LOWER(name_en) = ?', [strtolower($productNameEn)]);
                })
                ->first();

            if ($product) {
                $product->update([
                    'desc_ar' => $row['description_ar'] ?? $product->desc_ar,
                    'desc_en' => $row['description_en'] ?? $product->desc_en,
                    'brand_id' => $brand ? $brand->id : $product->brand_id,
                    'type' => $type,
                ]);
            } else {
                $product = Product::create([
                    'name_ar' => $productNameAr ?: $productNameEn,
                    'name_en' => $productNameEn ?: $productNameAr,
                    'desc_ar' => $row['description_ar'] ?? '',
                    'desc_en' => $row['description_en'] ?? '',
                    'brand_id' => $brand ? $brand->id : null,
                    'type' => $type,
                ]);
            }

            if ($category && !$product->categories()->where('category_id', $category->id)->exists()) {
                $product->categories()->attach($category->id);
            }

            // 4. Create Product Variant (Property)
            $price = $row['price'] ?? null;

            $wholesale_price = $row['wholesale_price'] ?? 0;
            $retail_price = $row['retail_price'] ?? 0;

            if ($price !== null) {
                if ($type === 'wholesale') {
                    $wholesale_price = $price;
                    if (!isset($row['retail_price']))
                        $retail_price = 0;
                } else {
                    $retail_price = $price;
                    if (!isset($row['wholesale_price']))
                        $wholesale_price = 0;
                }
            }
            $stock = $row['stock'] ?? 0;

            // Identity of the variant
            $currentConfig = [
                'product_id' => $product->id,
                'wholesale_price' => (float) $wholesale_price,
                'retail_price' => (float) $retail_price,
            ];

            // Match logic (Smart match: if current row is repeats product/prices, merge)
            if ($this->lastProperty && $this->lastPropertyConfig === $currentConfig) {
                $property = $this->lastProperty;
            } else {
                $property = new Property([
                    'product_id' => $product->id,
                    'wholesale_price' => $wholesale_price,
                    'retail_price' => $retail_price,
                    'stock' => $stock ?: 1,  // Default to 1 to avoid auto-delete
                    'min_quantity' => $row['min_quantity'] ?? 1,
                    'main_image' => $row['main_image_url'] ?? null,
                    'images' => !empty($row['other_images_urls']) ? explode(',', $row['other_images_urls']) : [],
                ]);
                $property->save();

                $this->lastProperty = $property;
                $this->lastPropertyConfig = $currentConfig;
            }

            // 5. Handle Images (Download and Store locally)
            $this->mergeImages($property, $row);

            // 6. Add Attribute(s) (Case-Insensitive)
            $this->importSeparatedAttributes($property->id, $row);
        }
    }

    private function mergeImages(Property $property, $row)
    {
        $updateNeeded = false;

        // 1. Handle Main Image
        $mainUrl = trim($row['main_image_url'] ?? '');
        if (!empty($mainUrl)) {
            $filename = $this->download($mainUrl);
            if ($filename && $property->main_image !== $filename) {
                $property->main_image = $filename;
                $updateNeeded = true;
            }
        }

        // 2. Handle Additional Images
        $othersStr = trim($row['other_images_urls'] ?? '');
        if (!empty($othersStr)) {
            $currentImages = is_array($property->images) ? $property->images : [];

            // CLEANUP: Always remove raw URLs, the current main image, and BROKEN paths from gallery
            $currentImages = array_filter($currentImages, function ($img) use ($property) {
                return !empty($img) &&
                    !filter_var($img, FILTER_VALIDATE_URL) &&
                    $img !== $property->main_image &&
                    Storage::disk('public')->exists('products/' . $img);
            });

            $urls = explode(',', $othersStr);
            foreach ($urls as $url) {
                $url = trim($url);
                if (empty($url))
                    continue;

                $filename = $this->download($url);
                // Only add if it's a valid local filename, not already there, and NOT the main image
                if ($filename && !in_array($filename, $currentImages) && $filename !== $property->main_image) {
                    $currentImages[] = $filename;
                    $updateNeeded = true;
                }
            }

            // Final safety: ensure uniqueness and reset keys
            $property->images = array_values(array_unique($currentImages));
        }

        if ($updateNeeded) {
            $property->save();
        }
    }

    private function download($url)
    {
        if (empty($url))
            return null;

        $isUrl = filter_var($url, FILTER_VALIDATE_URL);
        $cacheKey = strtolower($isUrl ? strtok($url, '?') : $url);

        // 1. Check Session Cache
        if (isset($this->downloadCache[$cacheKey])) {
            return $this->downloadCache[$cacheKey];
        }

        // 2. Handle ZIP Images or Local Filenames
        if (!$isUrl) {
            $foundPath = $this->extractedImagesPath ? $this->findFileInDir($this->extractedImagesPath, $url) : null;

            if ($foundPath) {
                $baseName = strtolower(basename($url));
                $filename = 'bynona_local_' . md5($baseName) . '_' . $baseName;

                if (!Storage::disk('public')->exists('products/' . $filename)) {
                    $content = file_get_contents($foundPath);
                    if ($content !== false) {
                        Storage::disk('public')->put('products/' . $filename, $content);
                    }
                }
                $this->downloadCache[$cacheKey] = $filename;
                return $filename;
            }

            // If not in ZIP, check if it already exists in project storage
            if (Storage::disk('public')->exists('products/' . $url)) {
                return $url;
            }

            return null;  // Return null to avoid broken icons
        }

        // 3. Handle Remote URLs
        $path = parse_url($cacheKey, PHP_URL_PATH);
        $ext = pathinfo($path, PATHINFO_EXTENSION) ?: 'jpg';
        $filename = 'bynona_' . md5($cacheKey) . '.' . $ext;

        if (Storage::disk('public')->exists('products/' . $filename)) {
            $this->downloadCache[$cacheKey] = $filename;
            return $filename;
        }

        try {
            $response = Http::timeout(15)->get($url);
            if ($response->successful()) {
                Storage::disk('public')->put('products/' . $filename, $response->body());
                $this->downloadCache[$cacheKey] = $filename;
                return $filename;
            }
        } catch (\Exception $e) {
            Log::error('Download failed for URL ' . $url . ': ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Helper to find a file recursively in a directory
     */
    private function findFileInDir($dir, $filename)
    {
        if (!is_dir($dir))
            return null;

        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..')
                continue;

            $fullPath = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_dir($fullPath)) {
                $res = $this->findFileInDir($fullPath, $filename);
                if ($res)
                    return $res;
            } elseif (strtolower($file) === strtolower($filename)) {
                return $fullPath;
            }
        }
        return null;
    }

    private function importSeparatedAttributes($propertyId, $row)
    {
        $keyAr = $row['key_ar'] ?? null;
        $keyEn = $row['key_en'] ?? null;
        $valAr = $row['value_ar'] ?? null;
        $valEn = $row['value_en'] ?? null;

        if (!empty($keyAr) || !empty($keyEn)) {
            $keyEn = $keyEn ?? $keyAr;

            $exists = Attribute::where('property_id', $propertyId)
                ->whereRaw('LOWER(key_en) = ?', [strtolower($keyEn)])
                ->exists();

            if (!$exists) {
                Attribute::create([
                    'property_id' => $propertyId,
                    'key_ar' => $keyAr ?? $keyEn,
                    'key_en' => $keyEn,
                    'value_ar' => $valAr ?? $row['value_en'],
                    'value_en' => $valEn ?? $row['value_ar'],
                ]);
            }
        }
    }
}
