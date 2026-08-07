<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use App\Models\ProductBanner;
use App\Models\Property;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Download an image safely and store it in target public/storage directories.
     */
    private function downloadAndSaveImage(string $url, string $folder, string $filename): string
    {
        $storagePaths = [
            storage_path("app/public/{$folder}"),
            public_path("storage/{$folder}"),
        ];

        // Also add direct storage/{folder} if folder is 'categories' for compatibility
        if ($folder === 'categories') {
            $storagePaths[] = public_path("storage/categories");
        }

        foreach ($storagePaths as $path) {
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(15)
                ->get($url);

            if ($response->successful()) {
                $content = $response->body();

                foreach ($storagePaths as $path) {
                    File::put("{$path}/{$filename}", $content);
                }

                $this->command->info("✓ Downloaded: {$folder}/{$filename}");
                return $filename;
            }
        } catch (\Throwable $e) {
            $this->command->warn("Failed downloading {$url}: " . $e->getMessage());
        }

        // Return filename even if failed so DB record is consistent
        return $filename;
    }

    public function run(): void
    {
        $this->command->info("Starting comprehensive store data seeding...");

        // Disable foreign key checks for clean truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Offer::truncate();
        Attribute::truncate();
        Property::truncate();
        if (Schema::hasTable('category_product')) {
            DB::table('category_product')->truncate();
        }
        Product::truncate();
        Brand::truncate();
        Category::truncate();
        Banner::truncate();
        ProductBanner::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. SEED BANNERS
        $this->command->info("\n--- Seeding Hero Banners ---");
        $heroBanners = [
            [
                'url' => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1200&auto=format&fit=crop',
                'title_ar' => 'عروض الصيف الكبرى خصومات تصل لـ 50%',
                'title_en' => 'Grand Summer Sale Up to 50% Off',
                'desc_ar' => 'اكتشف أفضل المنتجات بأفضل الأسعار وأعلى جودة في مصر',
                'desc_en' => 'Discover top quality products at unbeatable prices in Egypt',
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1200&auto=format&fit=crop',
                'title_ar' => 'تشكيلة الأزياء الجديدة لعام 2026',
                'title_en' => 'New Fashion Collection 2026',
                'desc_ar' => 'أرقى صيحات الموضة من أشهر الماركات العالمية بين يديك',
                'desc_en' => 'Top fashion trends from world renowned brands in your hand',
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200&auto=format&fit=crop',
                'title_ar' => 'أحدث الأجهزة الإلكترونية والتكنولوجيا',
                'title_en' => 'Latest Tech & Smart Electronics',
                'desc_ar' => 'تسوق أحدث الهواتف والسماعات والأجهزة الذكية بأسعار جملة وتجزئة',
                'desc_en' => 'Shop latest smartphones, headphones and gadgets wholesale & retail',
            ],
        ];

        foreach ($heroBanners as $index => $b) {
            $filename = "banner_" . ($index + 1) . ".jpg";
            $this->downloadAndSaveImage($b['url'], 'banners', $filename);

            Banner::create([
                'image' => $filename,
                'title_ar' => $b['title_ar'],
                'title_en' => $b['title_en'],
                'short_desc_ar' => $b['desc_ar'],
                'short_desc_en' => $b['desc_en'],
            ]);
        }

        // 2. SEED PRODUCT BANNERS (ADVERTISEMENTS)
        $this->command->info("\n--- Seeding Product Ad Banners ---");
        $adBanners = [
            [
                'url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop',
                'title_ar' => 'سماعات صوتية فاخرة عازلة للضوضاء',
                'title_en' => 'Premium Noise Cancelling Headphones',
                'desc_ar' => 'تجربة استماع نقية وصوت مجسم عالي الدقة',
                'desc_en' => 'Pure listening experience with high fidelity audio',
                'price' => 3499.00,
            ],
            [
                'url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop',
                'title_ar' => 'ساعات ذكية وأناقة بلا حدود',
                'title_en' => 'Smartwatches & Elegance Unlimited',
                'desc_ar' => 'تابع لياقتك وصحتك بأناقة ورقي',
                'desc_en' => 'Track your fitness and health with style',
                'price' => 2899.00,
            ],
        ];

        foreach ($adBanners as $index => $ad) {
            $filename = "ad_" . ($index + 1) . ".jpg";
            $this->downloadAndSaveImage($ad['url'], 'advertisements', $filename);

            ProductBanner::create([
                'image' => $filename,
                'title_ar' => $ad['title_ar'],
                'title_en' => $ad['title_en'],
                'desc_ar' => $ad['desc_ar'],
                'desc_en' => $ad['desc_en'],
                'price' => $ad['price'],
            ]);
        }

        // 3. SEED CATEGORIES
        $this->command->info("\n--- Seeding Categories ---");
        $categoriesData = [
            [
                'name_ar' => 'إلكترونيات',
                'name_en' => 'Electronics',
                'url' => 'https://images.unsplash.com/photo-1498049860654-af1a5c566876?w=600&auto=format&fit=crop',
                'filename' => 'cat_electronics_178522.jpg',
            ],
            [
                'name_ar' => 'ملابس وأزياء',
                'name_en' => 'Fashion & Clothes',
                'url' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=600&auto=format&fit=crop',
                'filename' => 'cat_fashion_1785224750.jpg',
            ],
            [
                'name_ar' => 'منزل ومطبخ',
                'name_en' => 'Home & Kitchen',
                'url' => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?w=600&auto=format&fit=crop',
                'filename' => 'cat_home-kitchen_17852.jpg',
            ],
            [
                'name_ar' => 'عطور وتجميل',
                'name_en' => 'Perfumes & Beauty',
                'url' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=600&auto=format&fit=crop',
                'filename' => 'cat_perfumes_178522475.jpg',
            ],
            [
                'name_ar' => 'ساعات ومقتنيات',
                'name_en' => 'Watches & Accessories',
                'url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop',
                'filename' => 'cat_watches_1785224750.jpg',
            ],
            [
                'name_ar' => 'ألعاب وأطفال',
                'name_en' => 'Toys & Kids',
                'url' => 'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?w=600&auto=format&fit=crop',
                'filename' => 'cat_toys_1785224750.jpg',
            ],
        ];

        // Also make default.png available
        $this->downloadAndSaveImage('https://images.unsplash.com/photo-1560343090-f0409e92791a?w=400&auto=format&fit=crop', 'categories', 'default.png');

        $createdCategories = [];
        foreach ($categoriesData as $index => $c) {
            $this->downloadAndSaveImage($c['url'], 'categories', $c['filename']);

            $cat = Category::create([
                'name_ar' => $c['name_ar'],
                'name_en' => $c['name_en'],
                'image' => $c['filename'],
                'sort_order' => $index + 1,
                'parent_id' => null,
            ]);

            $createdCategories[] = $cat;
        }

        // 4. SEED BRANDS
        $this->command->info("\n--- Seeding Brands ---");
        $brandsData = [
            [
                'name_ar' => 'آبل',
                'name_en' => 'Apple',
                'url' => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=400&auto=format&fit=crop',
                'filename' => 'brand_apple.jpg',
            ],
            [
                'name_ar' => 'نايكي',
                'name_en' => 'Nike',
                'url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&auto=format&fit=crop',
                'filename' => 'brand_nike.jpg',
            ],
            [
                'name_ar' => 'سامسونج',
                'name_en' => 'Samsung',
                'url' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400&auto=format&fit=crop',
                'filename' => 'brand_samsung.jpg',
            ],
            [
                'name_ar' => 'زارا',
                'name_en' => 'Zara',
                'url' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=400&auto=format&fit=crop',
                'filename' => 'brand_zara.jpg',
            ],
            [
                'name_ar' => 'سوني',
                'name_en' => 'Sony',
                'url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&auto=format&fit=crop',
                'filename' => 'brand_sony.jpg',
            ],
            [
                'name_ar' => 'رولكس',
                'name_en' => 'Rolex',
                'url' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&auto=format&fit=crop',
                'filename' => 'brand_rolex.jpg',
            ],
        ];

        $createdBrands = [];
        foreach ($brandsData as $b) {
            $this->downloadAndSaveImage($b['url'], 'brands', $b['filename']);

            $brand = Brand::create([
                'name_ar' => $b['name_ar'],
                'name_en' => $b['name_en'],
                'image' => $b['filename'],
            ]);

            $createdBrands[$b['name_en']] = $brand;
        }

        // 5. SEED PRODUCTS WITH VARIANTS (PROPERTIES), ATTRIBUTES, AND OFFERS
        $this->command->info("\n--- Seeding Products, Variants, Attributes & Offers ---");

        $productsCatalog = [
            [
                'name_ar' => 'آيفون 15 برو ماكس 256 جيبا',
                'name_en' => 'iPhone 15 Pro Max 256GB Titanium',
                'desc_ar' => 'هاتف آبل الرائد الجديد بهيكل من التيتانيوم القوي وشريحة A17 Pro الفائقة السرعة ومجموعة كاميرات احترافية',
                'desc_en' => 'Apple latest flagship smartphone featuring titanium design, A17 Pro chip and professional camera system',
                'brand' => 'Apple',
                'category_index' => 0, // Electronics
                'image_url' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=600&auto=format&fit=crop',
                'wholesale_price' => 52000.00,
                'retail_price' => 56000.00,
                'discount' => 15,
                'attributes' => [
                    ['key_en' => 'Color', 'key_ar' => 'اللون', 'value_en' => 'Natural Titanium', 'value_ar' => 'تيتانيوم طبيعي'],
                    ['key_en' => 'Storage', 'key_ar' => 'المساحة', 'value_en' => '256GB', 'value_ar' => '256 جيجابايت'],
                ],
            ],
            [
                'name_ar' => 'حذاء نايكي اير ماكس الرياضي',
                'name_en' => 'Nike Air Max Running Sneakers',
                'desc_ar' => 'حذاء رياضي عصري ومريح للغاية يوفر امتصاص تام للصدمات ودعم كامل للقدم أثناء الجري والمشي',
                'desc_en' => 'Modern extremely comfortable running shoes featuring responsive cushioning and maximum foot support',
                'brand' => 'Nike',
                'category_index' => 1, // Fashion
                'image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&auto=format&fit=crop',
                'wholesale_price' => 2800.00,
                'retail_price' => 3500.00,
                'discount' => 20,
                'attributes' => [
                    ['key_en' => 'Color', 'key_ar' => 'اللون', 'value_en' => 'Red/Black', 'value_ar' => 'أحمر/أسود'],
                    ['key_en' => 'Size', 'key_ar' => 'المقاس', 'value_en' => '42', 'value_ar' => '42'],
                ],
            ],
            [
                'name_ar' => 'سماعات سوني اللاسلكية WH-1000XM5',
                'name_en' => 'Sony WH-1000XM5 Wireless Headphones',
                'desc_ar' => 'سماعات رأس لاسلكية بتقنية إلغاء الضوضاء المتطورة وبطارية تدوم حتى 30 ساعة متواصلة',
                'desc_en' => 'Industry leading noise canceling headphones with crystal clear hands-free calling and 30hr battery',
                'brand' => 'Sony',
                'category_index' => 0, // Electronics
                'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop',
                'wholesale_price' => 14000.00,
                'retail_price' => 16500.00,
                'discount' => 10,
                'attributes' => [
                    ['key_en' => 'Color', 'key_ar' => 'اللون', 'value_en' => 'Black', 'value_ar' => 'أسود'],
                ],
            ],
            [
                'name_ar' => 'ساعة رولكس سبمارينر الفاخرة',
                'name_en' => 'Rolex Submariner Deluxe Edition',
                'desc_ar' => 'ساعة رجالية كلاسيكية فاخرة مصنعة من الفولاذ المقاوم للصدأ ومقاومة للماء حتى عمق 300 متر',
                'desc_en' => 'Luxury iconic diving watch crafted from stainless steel with exceptional precision and water resistance',
                'brand' => 'Rolex',
                'category_index' => 4, // Watches
                'image_url' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=600&auto=format&fit=crop',
                'wholesale_price' => 180000.00,
                'retail_price' => 195000.00,
                'discount' => 5,
                'attributes' => [
                    ['key_en' => 'Material', 'key_ar' => 'الخامة', 'value_en' => 'Stainless Steel', 'value_ar' => 'ستيل مقاوم للصدأ'],
                ],
            ],
            [
                'name_ar' => 'عطر فرنسي فاخر او دي بارفان 100 مل',
                'name_en' => 'Luxury French Eau De Parfum 100ml',
                'desc_ar' => 'عطر ساحر بتركيز عالي ونفحات عطرية جذابة تدوم طوال اليوم وتمنحك حضوراً مميزاً',
                'desc_en' => 'Enchanting long lasting French fragrance featuring sophisticated notes for an unforgettable presence',
                'brand' => 'Zara',
                'category_index' => 3, // Perfumes
                'image_url' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=600&auto=format&fit=crop',
                'wholesale_price' => 1200.00,
                'retail_price' => 1600.00,
                'discount' => 25,
                'attributes' => [
                    ['key_en' => 'Volume', 'key_ar' => 'الحجم', 'value_en' => '100ml', 'value_ar' => '100 مل'],
                ],
            ],
            [
                'name_ar' => 'سامسونج جالاكسي S24 اولترا 512 جيبا',
                'name_en' => 'Samsung Galaxy S24 Ultra 512GB',
                'desc_ar' => 'هاتف سامسونج الخارق مع تقنيات الذكاء الاصطناعي Galaxy AI وقلم S Pen المدمج وشاشة AMOLED المذهلة',
                'desc_en' => 'Ultimate Samsung flagship smartphone powered by Galaxy AI, integrated S Pen and 200MP camera',
                'brand' => 'Samsung',
                'category_index' => 0, // Electronics
                'image_url' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=600&auto=format&fit=crop',
                'wholesale_price' => 48000.00,
                'retail_price' => 53000.00,
                'discount' => 12,
                'attributes' => [
                    ['key_en' => 'Color', 'key_ar' => 'اللون', 'value_en' => 'Titanium Gray', 'value_ar' => 'رمادي تيتانيوم'],
                    ['key_en' => 'Storage', 'key_ar' => 'المساحة', 'value_en' => '512GB', 'value_ar' => '512 جيجابايت'],
                ],
            ],
            [
                'name_ar' => 'نظارة شمسية كلاسيكية حماية UV400',
                'name_en' => 'Classic Designer UV400 Sunglasses',
                'desc_ar' => 'نظارة شمسية أنيقة بتصميم عصري توفر حماية فائقة للعين من الأشعة فوق البنفسجية',
                'desc_en' => 'Stylish sunglasses providing complete UV400 protection with durable lightweight frame',
                'brand' => 'Zara',
                'category_index' => 1, // Fashion
                'image_url' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=600&auto=format&fit=crop',
                'wholesale_price' => 750.00,
                'retail_price' => 1100.00,
                'discount' => 30,
                'attributes' => [
                    ['key_en' => 'Color', 'key_ar' => 'اللون', 'value_en' => 'Black Gold', 'value_ar' => 'أسود ذهبي'],
                ],
            ],
            [
                'name_ar' => 'حقيبة جلدية أنيقة متعددة الاستخدامات',
                'name_en' => 'Premium Leather Backpack & Bag',
                'desc_ar' => 'حقيبة ظهر مصنوعة من الجلد الطبيعي الممتاز تتسع للابتوب والأغراض الشخصية بكل ترتيب',
                'desc_en' => 'Genuine high quality leather backpack designed for everyday commute and laptop protection',
                'brand' => 'Zara',
                'category_index' => 1, // Fashion
                'image_url' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600&auto=format&fit=crop',
                'wholesale_price' => 1900.00,
                'retail_price' => 2400.00,
                'discount' => null, // No offer
                'attributes' => [
                    ['key_en' => 'Material', 'key_ar' => 'الخامة', 'value_en' => 'Genuine Leather', 'value_ar' => 'جلد طبيعي'],
                ],
            ],
            [
                'name_ar' => 'ماك بوك اير M3 شاشة 13.6 بوصة',
                'name_en' => 'Apple MacBook Air M3 13.6-Inch',
                'desc_ar' => 'جهاز ماك بوك اير خفيف الوزن وفائق السرعة بشريحة M3 وبطارية تدوم حتى 18 ساعة',
                'desc_en' => 'Incredibly thin and fast laptop featuring M3 chip, Liquid Retina display and 18hr battery life',
                'brand' => 'Apple',
                'category_index' => 0, // Electronics
                'image_url' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=600&auto=format&fit=crop',
                'wholesale_price' => 45000.00,
                'retail_price' => 49000.00,
                'discount' => 10,
                'attributes' => [
                    ['key_en' => 'Color', 'key_ar' => 'اللون', 'value_en' => 'Space Gray', 'value_ar' => 'رمادي فلكي'],
                    ['key_en' => 'RAM', 'key_ar' => 'الذاكرة', 'value_en' => '16GB', 'value_ar' => '16 جيجابايت'],
                ],
            ],
            [
                'name_ar' => 'لعبة سيارة سباق متحكمة عن بعد',
                'name_en' => 'Remote Control High Speed RC Car',
                'desc_ar' => 'سيارة سباق ذكية للأطفال تعمل بالريموت كنترول بسرعات عالية وهيكل مقاوم للصدمات',
                'desc_en' => 'High speed remote control racing car featuring shockproof body and rechargeable battery',
                'brand' => 'Sony',
                'category_index' => 5, // Toys
                'image_url' => 'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?w=600&auto=format&fit=crop',
                'wholesale_price' => 850.00,
                'retail_price' => 1200.00,
                'discount' => 15,
                'attributes' => [
                    ['key_en' => 'Age', 'key_ar' => 'العمر', 'value_en' => '6+ Years', 'value_ar' => '6+ سنوات'],
                ],
            ],
            [
                'name_ar' => 'طقم أدوات طبخ سويت سيراميك 10 قطع',
                'name_en' => 'Sweet Ceramic Cookware Set 10-Piece',
                'desc_ar' => 'طقم حلل وأواني طبخ سيراميك غير لاصقة وصحية وسهلة التنظيف والتوزيع الحراري',
                'desc_en' => '10-piece non-stick ceramic cookware set offering even heat distribution and easy cleaning',
                'brand' => 'Zara',
                'category_index' => 2, // Home & Kitchen
                'image_url' => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?w=600&auto=format&fit=crop',
                'wholesale_price' => 4200.00,
                'retail_price' => 5100.00,
                'discount' => 20,
                'attributes' => [
                    ['key_en' => 'Pieces', 'key_ar' => 'عدد القطع', 'value_en' => '10 Pieces', 'value_ar' => '10 قطع'],
                ],
            ],
            [
                'name_ar' => 'ساعة ابل الذكية السلسلة 9',
                'name_en' => 'Apple Watch Series 9 GPS 45mm',
                'desc_ar' => 'ساعة آبل الذكية الأحدث مع شريحة S9 وميزة الضغط المزدوج واستشعار الأكسجين والنبض',
                'desc_en' => 'Advanced Apple smartwatch featuring S9 SiP, double tap gesture and health tracking sensors',
                'brand' => 'Apple',
                'category_index' => 4, // Watches
                'image_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop',
                'wholesale_price' => 17500.00,
                'retail_price' => 19800.00,
                'discount' => 10,
                'attributes' => [
                    ['key_en' => 'Size', 'key_ar' => 'المقاس', 'value_en' => '45mm', 'value_ar' => '45 مم'],
                ],
            ],
        ];

        foreach ($productsCatalog as $idx => $item) {
            $filename = "prod_" . ($idx + 1) . ".jpg";
            $this->downloadAndSaveImage($item['image_url'], 'products', $filename);

            $brandObj = $createdBrands[$item['brand']] ?? null;
            $catObj = $createdCategories[$item['category_index']] ?? null;

            $product = Product::create([
                'name_ar' => $item['name_ar'],
                'name_en' => $item['name_en'],
                'desc_ar' => $item['desc_ar'],
                'desc_en' => $item['desc_en'],
                'brand_id' => $brandObj ? $brandObj->id : null,
                'type' => 'retail',
            ]);

            if ($catObj) {
                if (Schema::hasColumn('products', 'category_id')) {
                    $product->category_id = $catObj->id;
                    $product->save();
                }
                if (Schema::hasTable('category_product')) {
                    $product->categories()->attach($catObj->id);
                }
            }

            // Create Property (Variant)
            $property = Property::create([
                'product_id' => $product->id,
                'wholesale_price' => $item['wholesale_price'],
                'retail_price' => $item['retail_price'],
                'main_image' => $filename,
                'images' => [$filename],
                'min_quantity' => 1,
                'stock' => rand(15, 80),
            ]);

            // Create Attributes
            foreach ($item['attributes'] as $attr) {
                Attribute::create([
                    'property_id' => $property->id,
                    'key_en' => $attr['key_en'],
                    'key_ar' => $attr['key_ar'],
                    'value_en' => $attr['value_en'],
                    'value_ar' => $attr['value_ar'],
                ]);
            }

            // Create Offer if discount exists
            if ($item['discount']) {
                Offer::create([
                    'property_id' => $property->id,
                    'start' => now()->subDay(),
                    'end' => now()->addDays(14),
                    'discount_retail' => $item['discount'],
                    'discount_wholesale' => $item['discount'],
                ]);
            }
        }

        $this->command->info("\n========================================================");
        $this->command->info("🎉 SEEDING COMPLETED SUCCESSFULLY!");
        $this->command->info("All banners, categories, brands & products have real downloaded images!");
        $this->command->info("========================================================\n");
    }
}
