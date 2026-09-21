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
            public_path("storage/app/public/{$folder}"),
        ];

        if ($folder === 'categories') {
            $storagePaths[] = public_path("storage/categories");
        }

        foreach ($storagePaths as $path) {
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
        }

        // Check if file already exists in primary storage path
        $primaryFile = "{$storagePaths[0]}/{$filename}";
        if (File::exists($primaryFile) && filesize($primaryFile) > 0) {
            return $filename;
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(10)
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

        // 2. SEED PRODUCT BANNERS (ADVERTISEMENTS) - 3 BANNERS
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
            [
                'url' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop',
                'title_ar' => 'آيفون 15 برو ماكس جديد بالتيتانيوم',
                'title_en' => 'New iPhone 15 Pro Max Titanium',
                'desc_ar' => 'أقوى هاتف في العالم مع كاميرا احترافية وشريحة A17 Pro الفائقة',
                'desc_en' => 'Powerful flagship phone with pro camera & A17 Pro chip',
                'price' => 54999.00,
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
            ['name_ar' => 'آبل', 'name_en' => 'Apple', 'url' => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=400&auto=format&fit=crop', 'filename' => 'brand_apple.jpg'],
            ['name_ar' => 'نايكي', 'name_en' => 'Nike', 'url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&auto=format&fit=crop', 'filename' => 'brand_nike.jpg'],
            ['name_ar' => 'سامسونج', 'name_en' => 'Samsung', 'url' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=400&auto=format&fit=crop', 'filename' => 'brand_samsung.jpg'],
            ['name_ar' => 'زارا', 'name_en' => 'Zara', 'url' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=400&auto=format&fit=crop', 'filename' => 'brand_zara.jpg'],
            ['name_ar' => 'سوني', 'name_en' => 'Sony', 'url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&auto=format&fit=crop', 'filename' => 'brand_sony.jpg'],
            ['name_ar' => 'رولكس', 'name_en' => 'Rolex', 'url' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&auto=format&fit=crop', 'filename' => 'brand_rolex.jpg'],
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

        // 5. SEED PRODUCTS (50 WHOLESALE + 50 RETAIL)
        $this->command->info("\n--- Seeding 50 Wholesale & 50 Retail Products ---");

        // Curated Product Images Pool
        $imageUrls = [
            'iphone' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=600&auto=format&fit=crop',
            'sony_headphone' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop',
            'nike_shoes' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&auto=format&fit=crop',
            'samsung_phone' => 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=600&auto=format&fit=crop',
            'perfume' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=600&auto=format&fit=crop',
            'rolex_watch' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=600&auto=format&fit=crop',
            'macbook' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=600&auto=format&fit=crop',
            'smartwatch' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop',
            'cookware' => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?w=600&auto=format&fit=crop',
            'sunglasses' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?w=600&auto=format&fit=crop',
            'backpack' => 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=600&auto=format&fit=crop',
            'toy_car' => 'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?w=600&auto=format&fit=crop',
            'gadget' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=600&auto=format&fit=crop',
            'fashion' => 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=600&auto=format&fit=crop',
            'speaker' => 'https://images.unsplash.com/photo-1545454675-3531b543be5d?w=600&auto=format&fit=crop',
        ];

        // Download base images once
        $imageFiles = [];
        foreach ($imageUrls as $key => $url) {
            $fn = "prod_img_{$key}.jpg";
            $this->downloadAndSaveImage($url, 'products', $fn);
            $imageFiles[$key] = $fn;
        }

        // Shared 6 products template (same name, desc, brand, category, image, attributes but different prices/type)
        $sharedProductsSpec = [
            [
                'name_ar' => 'آيفون 15 برو ماكس 256 جيجا تيتانيوم',
                'name_en' => 'iPhone 15 Pro Max 256GB Titanium',
                'desc_ar' => 'هاتف آبل الرائد ببدن من التيتانيوم وشريحة A17 Pro الفائقة ومجموعة كاميرات احترافية',
                'desc_en' => 'Apple flagship smartphone featuring titanium design, A17 Pro chip and professional camera system',
                'brand' => 'Apple',
                'cat' => 0, // Electronics
                'img' => $imageFiles['iphone'],
                'wholesale_price' => 49500.00,
                'retail_price' => 56000.00,
                'attributes' => [
                    ['key_en' => 'Color', 'key_ar' => 'اللون', 'value_en' => 'Natural Titanium', 'value_ar' => 'تيتانيوم طبيعي'],
                    ['key_en' => 'Storage', 'key_ar' => 'المساحة', 'value_en' => '256GB', 'value_ar' => '256 جيجابايت'],
                ],
            ],
            [
                'name_ar' => 'سماعات سوني اللاسلكية WH-1000XM5',
                'name_en' => 'Sony WH-1000XM5 Wireless Headphones',
                'desc_ar' => 'سماعات رأس لاسلكية احترافية بتقنية عزل الضوضاء المتطورة وبطارية تدوم حتى 30 ساعة',
                'desc_en' => 'Industry leading noise canceling headphones with crystal clear sound and 30hr battery life',
                'brand' => 'Sony',
                'cat' => 0, // Electronics
                'img' => $imageFiles['sony_headphone'],
                'wholesale_price' => 13200.00,
                'retail_price' => 16500.00,
                'attributes' => [
                    ['key_en' => 'Color', 'key_ar' => 'اللون', 'value_en' => 'Black', 'value_ar' => 'أسود'],
                    ['key_en' => 'Connectivity', 'key_ar' => 'التوصيل', 'value_en' => 'Bluetooth 5.2', 'value_ar' => 'بلوتوث 5.2'],
                ],
            ],
            [
                'name_ar' => 'حذاء نايكي اير ماكس الرياضي',
                'name_en' => 'Nike Air Max Running Sneakers',
                'desc_ar' => 'حذاء رياضي عصري مريح يوفر امتصاص تام للصدمات ودعم كامل للقدم أثناء الجري',
                'desc_en' => 'Modern extremely comfortable running shoes featuring responsive cushioning and foot support',
                'brand' => 'Nike',
                'cat' => 1, // Fashion
                'img' => $imageFiles['nike_shoes'],
                'wholesale_price' => 2700.00,
                'retail_price' => 3800.00,
                'attributes' => [
                    ['key_en' => 'Color', 'key_ar' => 'اللون', 'value_en' => 'Red/Black', 'value_ar' => 'أحمر/أسود'],
                    ['key_en' => 'Size', 'key_ar' => 'المقاس', 'value_en' => '42', 'value_ar' => '42'],
                ],
            ],
            [
                'name_ar' => 'سامسونج جالاكسي S24 اولترا 512 جيجا',
                'name_en' => 'Samsung Galaxy S24 Ultra 512GB',
                'desc_ar' => 'هاتف سامسونج الخارق مع تقنيات الذكاء الاصطناعي Galaxy AI وقلم S Pen وشاشة AMOLED',
                'desc_en' => 'Ultimate Samsung flagship smartphone powered by Galaxy AI, integrated S Pen and 200MP camera',
                'brand' => 'Samsung',
                'cat' => 0, // Electronics
                'img' => $imageFiles['samsung_phone'],
                'wholesale_price' => 45000.00,
                'retail_price' => 52000.00,
                'attributes' => [
                    ['key_en' => 'Color', 'key_ar' => 'اللون', 'value_en' => 'Titanium Gray', 'value_ar' => 'رمادي تيتانيوم'],
                    ['key_en' => 'Storage', 'key_ar' => 'المساحة', 'value_en' => '512GB', 'value_ar' => '512 جيجابايت'],
                ],
            ],
            [
                'name_ar' => 'عطر فرنسي فاخر او دي بارفان 100 مل',
                'name_en' => 'Luxury French Eau De Parfum 100ml',
                'desc_ar' => 'عطر ساحر بتركيز عالي ونفحات عطرية جذابة تدوم طوال اليوم وتمنحك حضوراً مميزاً',
                'desc_en' => 'Enchanting long lasting French fragrance featuring sophisticated notes for an unforgettable presence',
                'brand' => 'Zara',
                'cat' => 3, // Perfumes
                'img' => $imageFiles['perfume'],
                'wholesale_price' => 1250.00,
                'retail_price' => 1800.00,
                'attributes' => [
                    ['key_en' => 'Volume', 'key_ar' => 'الحجم', 'value_en' => '100ml', 'value_ar' => '100 مل'],
                ],
            ],
            [
                'name_ar' => 'ساعة رولكس سبمارينر الفاخرة',
                'name_en' => 'Rolex Submariner Deluxe Edition',
                'desc_ar' => 'ساعة رجالية كلاسيكية فاخرة مصنعة من الفولاذ المقاوم للصدأ ومقاومة للماء حتى عمق 300 متر',
                'desc_en' => 'Luxury iconic diving watch crafted from stainless steel with exceptional precision and water resistance',
                'brand' => 'Rolex',
                'cat' => 4, // Watches
                'img' => $imageFiles['rolex_watch'],
                'wholesale_price' => 175000.00,
                'retail_price' => 195000.00,
                'attributes' => [
                    ['key_en' => 'Material', 'key_ar' => 'الخامة', 'value_en' => 'Stainless Steel', 'value_ar' => 'ستيل مقاوم للصدأ'],
                ],
            ],
        ];

        // Build list of products to seed
        $allProducts = [];

        // Add Wholesale version of shared products (6 items)
        foreach ($sharedProductsSpec as $s) {
            $allProducts[] = [
                'type' => 'wholesale',
                'name_ar' => $s['name_ar'],
                'name_en' => $s['name_en'],
                'desc_ar' => $s['desc_ar'],
                'desc_en' => $s['desc_en'],
                'brand' => $s['brand'],
                'cat' => $s['cat'],
                'img' => $s['img'],
                'price' => $s['wholesale_price'],
                'wholesale_price' => $s['wholesale_price'],
                'retail_price' => $s['retail_price'],
                'min_qty' => 5,
                'discount' => 15,
                'attributes' => $s['attributes'],
            ];
        }

        // Add Retail version of shared products (6 items - exact same name, image, attributes, but retail price and type)
        foreach ($sharedProductsSpec as $s) {
            $allProducts[] = [
                'type' => 'retail',
                'name_ar' => $s['name_ar'],
                'name_en' => $s['name_en'],
                'desc_ar' => $s['desc_ar'],
                'desc_en' => $s['desc_en'],
                'brand' => $s['brand'],
                'cat' => $s['cat'],
                'img' => $s['img'],
                'price' => $s['retail_price'],
                'wholesale_price' => $s['wholesale_price'],
                'retail_price' => $s['retail_price'],
                'min_qty' => 1,
                'discount' => 10,
                'attributes' => $s['attributes'],
            ];
        }

        // 44 Unique Wholesale Products
        $uniqueWholesaleProducts = [
            ['كرتونة شواحن آبل السريعة 20 واط (50 قطعة)', 'Apple 20W Fast Charger Carton (50 Pcs)', 'كرتونة شواحن سريعة معتمدة ومطابقة للمواصفات جملة', 'Box of 50 certified 20W USB-C fast charging adapters', 'Apple', 0, $imageFiles['gadget'], 12500.00, 10],
            ['كابلات سامسونج تايب سي الأصلية (100 قطعة)', 'Original Samsung Type-C Cable Bulk (100 Pcs)', 'طرد كابلات شحن ونقل بيانات سريعة 100 قطعة جملة', 'Pack of 100 high speed data and charging Type-C cables', 'Samsung', 0, $imageFiles['gadget'], 8000.00, 10],
            ['طرد تيشيرتات قطن نايكي رجالي (30 قطعة)', 'Nike Men Cotton T-Shirts Pack (30 Pcs)', 'تشكيلة تيشيرتات قطن 100% مقاسات وألوان متنوعة جملة', 'Assorted colors and sizes pack of 30 Nike cotton shirts', 'Nike', 1, $imageFiles['fashion'], 10500.00, 5],
            ['مجموعة ساعات رولكس كلاسيك جملة (5 قطع)', 'Rolex Classic Watches Wholesale Lot (5 Pcs)', 'طقم ساعات كلاسيكية ستانلس ستيل جملة للتجار والوكلاء', 'Exclusive lot of 5 stainless steel luxury classic timepieces', 'Rolex', 4, $imageFiles['rolex_watch'], 750000.00, 1],
            ['دستة عطور زارا النسائية المتنوعة (12 زجاجة)', 'Zara Women Perfumes Assorted Dozen (12 Pcs)', 'عطور نسائية راقية بثبات عالي 12 زجاجة مشكلة جملة', '12 Bottles of premium long-lasting women perfume fragrances', 'Zara', 3, $imageFiles['perfume'], 9600.00, 12],
            ['شاشات سامسونج سمارت 55 بوصة جملة (10 شاشات)', 'Samsung 55-Inch Smart TV Bulk (10 Pcs)', 'دفعة شاشات كريستال فوركيه سمارت جملة مع ضمان سنتين', 'Bulk order of 10 Samsung 4K Crystal UHD Smart TVs with warranty', 'Samsung', 0, $imageFiles['samsung_phone'], 190000.00, 2],
            ['كرتونة سماعات بلوتوث ايربودز جملة (25 قطعة)', 'AirPods Pro Bluetooth Earphones Box (25 Pcs)', 'سماعات لاسلكية عالية الجودة بحافظة شحن مجسمة جملة', '25 Units of spatial audio noise canceling wireless earbuds', 'Apple', 0, $imageFiles['sony_headphone'], 37500.00, 5],
            ['طرد بنطلونات جينز زارا رجالي (20 قطعة)', 'Zara Men Denim Jeans Pack (20 Pcs)', 'بنطلونات جينز عصرية خامات ممتازة مقاسات متعددة جملة', 'Pack of 20 premium denim jeans in modern fits and sizes', 'Zara', 1, $imageFiles['fashion'], 14000.00, 5],
            ['مجموعة خلاطات كهربائية للمطبخ 1000 واط (10 قطع)', 'Heavy Duty Kitchen Blenders 1000W (10 Pcs)', 'خلاطات مطبخ احترافية متعددة السرعات مع مطحنة جملة', '10 Professional 1000W countertop kitchen blenders with grinders', 'Sony', 2, $imageFiles['cookware'], 18000.00, 2],
            ['كرتونة مجموعات عناية بالبشرة والتجميل (48 قطعة)', 'Skincare & Beauty Care Sets Carton (48 Pcs)', 'مجموعات سيروم وكريمات مرطبة متكاملة للتجميل جملة', '48 Complete hydrating skincare and beauty cosmetic treatment kits', 'Zara', 3, $imageFiles['perfume'], 15000.00, 10],
            ['طقم أحذية رياضية نايكي جملة (15 زوج)', 'Nike Athletic Shoes Bulk Parcel (15 Pairs)', 'أحذية رياضية خفيفة للجري والجيم مقاسات مشكلة جملة', '15 Pairs of Nike lightweight breathable athletic sneakers', 'Nike', 1, $imageFiles['nike_shoes'], 33000.00, 3],
            ['باكت ألعاب سيارات بالريموت للأطفال (12 لعبة)', 'RC Remote Control Cars Pack (12 Pcs)', 'سيارات سباق بالريموت كنترول ببطاريات قابلة للشحن جملة', '12 High speed rechargeable remote control racing toy cars', 'Sony', 5, $imageFiles['toy_car'], 7200.00, 6],
            ['كرتونة باور بنك انكر 20000 ملي امبير (20 قطعة)', 'Anker 20000mAh Power Bank Carton (20 Pcs)', 'بطاريات شحن خارجية سريعة بمنفذين جملة للمحلات', '20 High capacity fast dual-port portable chargers', 'Apple', 0, $imageFiles['gadget'], 22000.00, 5],
            ['طقم حلل جرانيت كوين 12 قطعة جملة (5 أطقم)', 'Granite Cookware Sets 12-Pcs (5 Sets)', 'أطقم أواني طهي جرانيت غير لاصقة بتوزيع حراري ممتاز جملة', '5 Complete 12-piece non-stick granite cookware set lots', 'Zara', 2, $imageFiles['cookware'], 21000.00, 1],
            ['ساعات ذكية سامسونج جالاكسي جملة (10 قطع)', 'Samsung Galaxy Smartwatches Lot (10 Pcs)', 'ساعات ذكية لتتبع الصحة والرياضة وشاشة سوبر اموليد جملة', '10 Advanced health tracking AMOLED display smartwatches', 'Samsung', 4, $imageFiles['smartwatch'], 85000.00, 2],
            ['كرتونة مكائن حلاقة رجالية احترافية (30 قطعة)', 'Professional Men Hair Trimmers Carton (30 Pcs)', 'مكائن حلاقة للشعر والذقن ببطارية ليثيوم وشفرات حادة جملة', '30 Rechargeable precision beard and hair grooming trimmers', 'Sony', 0, $imageFiles['gadget'], 16500.00, 5],
            ['مجموعة أحزمة جلد طبيعي رجالي زارا (50 قطعة)', 'Zara Men Genuine Leather Belts (50 Pcs)', 'أحزمة جلد طبيعي بظافات ستانلس ألوان أسود وبني جملة', '50 Premium genuine leather dress belts in black and brown', 'Zara', 1, $imageFiles['backpack'], 12000.00, 10],
            ['طقم ماكينات قهوة اسبريسو للمقاهي (4 ماكينات)', 'Commercial Espresso Coffee Machines (4 Pcs)', 'ماكينات تحضير اسبريسو وبخار حليب احترافية جملة', '4 Commercial grade dual-boiler espresso coffee machines', 'Sony', 2, $imageFiles['cookware'], 64000.00, 1],
            ['مجموعة أطقم مكياج كاملة جملة (20 طقم)', 'Complete Makeup Artist Kits (20 Kits)', 'حقائب مكياج احترافية شاملة للظلال والروج والبودرة جملة', '20 All-in-one professional makeup artist cosmetic palettes', 'Zara', 3, $imageFiles['perfume'], 18000.00, 5],
            ['طرد جاكيتات شتوية نايكي مقاومة للماء (10 قطع)', 'Nike Waterproof Winter Jackets (10 Pcs)', 'جاكيتات شتوية مبطنة عازلة للحرارة ومقاومة للمطر جملة', '10 Insulated waterproof heavy duty winter puff jackets', 'Nike', 1, $imageFiles['fashion'], 22000.00, 2],
            ['مجموعة لابتوبات ماك بوك برو M3 جملة (5 أجهزة)', 'MacBook Pro M3 Laptops Bulk (5 Pcs)', 'لابتوبات آبل ماك بوك برو بشريحة M3 وذاكرة 16 جيجا جملة', '5 High performance M3 chip 16GB RAM MacBook Pro laptops', 'Apple', 0, $imageFiles['macbook'], 360000.00, 1],
            ['كرتونة أذرع تحكم بلايستيشن 5 أصلية (10 قطع)', 'PS5 DualSense Controllers Box (10 Pcs)', 'أذرع تحكم دوال سينس لاسلكية بلايستيشن 5 أصلية جملة', '10 Wireless DualSense gaming controllers for PlayStation 5', 'Sony', 0, $imageFiles['gadget'], 28000.00, 2],
            ['طقم نظارات شمسية قطبية زارا (30 قطعة)', 'Zara Polarized Sunglasses Bulk Pack (30 Pcs)', 'نظارات شمسية قطبية حماية UV400 تصاميم عصرية جملة', '30 Polarized designer UV400 protection sunglasses', 'Zara', 1, $imageFiles['sunglasses'], 13500.00, 5],
            ['كرتونة مكبرات صوت بلوتوث ضد الماء (15 قطعة)', 'Waterproof Bluetooth Speakers Box (15 Pcs)', 'سماعات بلوتوث محمولة بصوت جهير قوي ضد الماء جملة', '15 Portable IPX7 waterproof heavy bass Bluetooth speakers', 'Sony', 0, $imageFiles['speaker'], 16500.00, 3],
            ['مجموعة أواني وقلايات هوائية ذكية (8 قطع)', 'Smart Air Fryers Kitchen Bulk (8 Pcs)', 'قلايات هوائية بدون زيت سعة 6 لتر شاشة ديجيتال جملة', '8 Large 6-Liter digital touchscreen oil-free air fryers', 'Samsung', 2, $imageFiles['cookware'], 28000.00, 2],
            ['دستة عود ومعطرات جو فاخرة (12 علبة)', 'Luxury Oud & Room Air Fresheners (12 Pcs)', 'معطرات جو ومفارش برائحة العود والمسك الملكي جملة', '12 Premium Royal Oud and Musk home ambient room sprays', 'Zara', 3, $imageFiles['perfume'], 4800.00, 12],
            ['طرد حقائب سفر فايبر 3 مقاسات (5 أطقم)', 'Luggage Travel Suitcases Sets (5 Sets)', 'أطقم شنط سفر فايبر مقاسات كبيرة ومتوسطة وصغيرة جملة', '5 Complete 3-piece hard-shell spinner travel suitcase sets', 'Zara', 1, $imageFiles['backpack'], 24000.00, 1],
            ['كرتونة ألعاب مكعبات ليجو تعليمية (20 طقم)', 'Educational Building Blocks Toys (20 Sets)', 'ألعاب مكعبات بناء وتنمية مهارات الأطفال جملة', '20 STEM educational building block toy sets for kids', 'Sony', 5, $imageFiles['toy_car'], 11000.00, 5],
            ['طقم ساعات سويسرية فاخرة جملة (3 قطع)', 'Swiss Automatic Executive Watches (3 Pcs)', 'ساعات ميكانيكية ذاتية الدفع مينا سيراميك جملة', '3 Executive automatic mechanical movement wristwatches', 'Rolex', 4, $imageFiles['rolex_watch'], 240000.00, 1],
            ['طرد قمصان رجالي رسمية زارا (25 قطعة)', 'Zara Men Formal Dress Shirts (25 Pcs)', 'قمصان رسمية سليم فيت قطن ممتازة للبدل جملة', '25 Premium slim-fit cotton formal button-up shirts', 'Zara', 1, $imageFiles['fashion'], 15000.00, 5],
            ['كرتونة هاردات خارجية SSD 1 تيرا (15 قطعة)', 'Portable SSD 1TB Drives Carton (15 Pcs)', 'هاردات تخزين سريعة خارجية ضد الصدمات USB 3.2 جملة', '15 Shockproof 1TB portable solid state drives', 'Samsung', 0, $imageFiles['gadget'], 42000.00, 3],
            ['مجموعة مكنسات كهربائية ذكية روبوت (6 قطع)', 'Robot Vacuum Cleaners Bulk (6 Pcs)', 'مكنسات روبوت ذكية بتطبيق واي فاي ومسح ذكي جملة', '6 Smart Wi-Fi app-controlled robotic vacuum cleaners', 'Samsung', 2, $imageFiles['cookware'], 48000.00, 1],
            ['دستة زيوت عطري نادرة ومستحضرات تجميل (12 قطعة)', 'Rare Essential Oils & Beauty Serums (12 Pcs)', 'زيوت عطرية مئة بالمئة طبيعية للعناية والاسترخاء جملة', '12 Organic essential therapy oils and rejuvenating serums', 'Zara', 3, $imageFiles['perfume'], 8400.00, 12],
            ['طرد هوديز نايكي دافئة للأطفال (20 قطعة)', 'Nike Kids Warm Fleece Hoodies (20 Pcs)', 'سويت شيرتات هودي ميلتون مبطن للأطفال جملة', '20 Warm fleece fleece lined pullover hoodies for kids', 'Nike', 1, $imageFiles['fashion'], 11000.00, 5],
            ['كرتونة كاميرات مراقبة ذكية واي فاي (20 قطعة)', 'Smart WiFi Security Cameras Box (20 Pcs)', 'كاميرات مراقبة منزلية برؤية الليلية وتتبع الحركة جملة', '20 Night-vision motion tracking indoor smart security cameras', 'Sony', 0, $imageFiles['gadget'], 18000.00, 4],
            ['مجموعة طابعات ليزر ملونة للأعمال (4 طابعات)', 'Business Color Laser Printers Lot (4 Pcs)', 'طابعات ليزر سريعة مع واي فاي وتصوير جملة', '4 All-in-one wireless color laser multifunction printers', 'Samsung', 0, $imageFiles['gadget'], 52000.00, 1],
            ['طقم أغطية وملايات سرير قطن تركي (10 أطقم)', 'Turkish Cotton Bedding Sets Bulk (10 Sets)', 'أطقم ملايات قطن مطرزة فاخرة سرير كبير جملة', '10 Premium 100% Turkish cotton king bedding sheet sets', 'Zara', 2, $imageFiles['cookware'], 17000.00, 2],
            ['كرتونة ألعاب عرائس ومنازل أطفال (15 لعبة)', 'Dollhouses & Princess Toys Box (15 Pcs)', 'منازل عرائس خشبية متكاملة وملحقاتها جملة', '15 Multi-story dollhouse playsets with furniture accessories', 'Sony', 5, $imageFiles['toy_car'], 9000.00, 3],
            ['طرد شنط يدوية حريمي زارا راقية (15 قطعة)', 'Zara Luxury Women Handbags Pack (15 Pcs)', 'شنط يد حريمي خامات فاخرة وتصاميم إيطالية جملة', '15 Italian design elegant women shoulder and handbags', 'Zara', 1, $imageFiles['backpack'], 19500.00, 3],
            ['مجموعة تابلت سامسونج جالاكسي تاب S9 (8 أجهزة)', 'Samsung Galaxy Tab S9 Bulk (8 Pcs)', 'تابلت سامسونج مع قلم S Pen وشاشة AMOLED جملة', '8 Premium Samsung S Pen tablets with AMOLED displays', 'Samsung', 0, $imageFiles['samsung_phone'], 184000.00, 1],
            ['كرتونة موازين ديجيتال ومعدات صحية (25 قطعة)', 'Digital Smart Body Scales Box (25 Pcs)', 'موازين جسم ذكية تقيس نسبة الدهون والماء جملة', '25 Bluetooth smart body composition analyzer scales', 'Sony', 0, $imageFiles['smartwatch'], 12500.00, 5],
            ['طقم معاطف وجواكيت جلد رجالية (8 قطع)', 'Men Real Leather Jackets Parcel (8 Pcs)', 'معاطف شتوية من الجلد الطبيعي الممتاز جملة', '8 Genuine leather classic biter and winter coats', 'Zara', 1, $imageFiles['fashion'], 28000.00, 2],
            ['مجموعة أطقم أواني ضغط استانلس (6 قطع)', 'Stainless Steel Pressure Cookers (6 Pcs)', 'حلة ضغط طهي سريع أمان عالي سعة 9 لتر جملة', '6 High-safety 9-Liter stainless steel pressure cookers', 'Samsung', 2, $imageFiles['cookware'], 15600.00, 2],
            ['كرتونة دراجات أطفال وسكوترات (10 قطع)', 'Kids Scooters & Balance Bikes Box (10 Pcs)', 'سكوترات ومعدات ترفيهية للأطفال متينة جملة', '10 Adjustable handlebar foldable kids scooters with LED wheels', 'Nike', 5, $imageFiles['toy_car'], 14000.00, 2],
        ];

        foreach ($uniqueWholesaleProducts as $w) {
            $allProducts[] = [
                'type' => 'wholesale',
                'name_ar' => $w[0],
                'name_en' => $w[1],
                'desc_ar' => $w[2],
                'desc_en' => $w[3],
                'brand' => $w[4],
                'cat' => $w[5],
                'img' => $w[6],
                'price' => $w[7],
                'wholesale_price' => $w[7],
                'retail_price' => $w[7] * 1.25,
                'min_qty' => $w[8],
                'discount' => rand(0, 1) ? rand(5, 20) : null,
                'attributes' => [
                    ['key_en' => 'Package', 'key_ar' => 'التعبئة', 'value_en' => 'Bulk Box', 'value_ar' => 'كرتونة جملة'],
                ],
            ];
        }

        // 44 Unique Retail Products
        $uniqueRetailProducts = [
            ['ماك بوك اير M3 شاشة 13.6 بوصة', 'Apple MacBook Air M3 13.6-Inch', 'لابتوبات ماك بوك اير خفيف الوزن بشريحة M3 وبطارية 18 ساعة', 'Ultra thin laptop featuring M3 chip, Liquid Retina display and 18hr battery', 'Apple', 0, $imageFiles['macbook'], 49000.00],
            ['ساعة ابل الذكية السلسلة 9', 'Apple Watch Series 9 GPS 45mm', 'ساعة آبل الذكية مع شريحة S9 وميزة الضغط المزدوج واستشعار الأكسجين', 'Advanced Apple smartwatch with S9 SiP, double tap gesture and health sensors', 'Apple', 4, $imageFiles['smartwatch'], 19800.00],
            ['حذاء جري نايكي بيجاسوس 40', 'Nike Pegasus 40 Running Shoes', 'حذاء جري متطور يدعم القدم ومناسب للتمرين والمسافات الطويلة', 'Responsive everyday running shoes with neutral support and breathable mesh', 'Nike', 1, $imageFiles['nike_shoes'], 4200.00],
            ['شاشة سامسونج OLED 65 بوصة 4K', 'Samsung 65-Inch 4K Smart OLED TV', 'شاشة سامسونج اوليد ألوان فائقة وأعلى تباين مع معدل 120 هرتز', 'Stunning 65-inch 4K OLED Smart TV with Neural Quantum Processor', 'Samsung', 0, $imageFiles['samsung_phone'], 34500.00],
            ['سماعات ابل ايربودز برو الجيل الثاني', 'Apple AirPods Pro 2nd Gen USB-C', 'سماعات أبل اللاسلكية مع ميزة إلغاء الضوضاء النشط وصوت محيطي', 'Active Noise Cancellation and Transparency mode wireless earbuds', 'Apple', 0, $imageFiles['sony_headphone'], 9800.00],
            ['جاكيت جلد طبيعي رجالي زارا', 'Zara Men Real Leather Biker Jacket', 'جاكيت جلد طبيعي بقصة عصرية وبطانة مريحة جداً', 'Classic black real leather biker jacket with asymmetric zipper', 'Zara', 1, $imageFiles['fashion'], 4500.00],
            ['ماكينة اسبريسو ديلونجي ديديكا', 'DeLonghi Dedica Espresso Coffee Machine', 'ماكينة تحضير الاسبريسو والكبوتشينو الضغط 15 بار', 'Compact pump espresso machine with manual cappuccino system', 'Sony', 2, $imageFiles['cookware'], 8900.00],
            ['عطر شانيل بلو دو شانيل 100 مل', 'Bleu De Chanel Eau De Parfum 100ml', 'عطر أروما خشبية جذابة وأناقة فريدة للرجال', 'Aromatic-woody fragrance with a captivating trail for modern men', 'Zara', 3, $imageFiles['perfume'], 5400.00],
            ['ساعة رولكس ديت جست تيتانيوم', 'Rolex Datejust 41mm Automatic Watch', 'ساعة فاخرة كلاسيكية بسوار جوبيلي ومينا أزرق ملكي', 'Iconic luxury watch with Jubilee bracelet and blue dial', 'Rolex', 4, $imageFiles['rolex_watch'], 210000.00],
            ['قلاية هوائية فيليبس XXL سمارت', 'Philips Air Fryer XXL Smart 1.4kg', 'قلاية بدون زيت مع تقنية إزالة الدهون وشاشة رقمية', 'Fat Removal Technology air fryer with preset cooking programs', 'Samsung', 2, $imageFiles['cookware'], 7200.00],
            ['حذاء رياضي كاجوال نايكي جوردان', 'Nike Air Jordan 1 Retro High', 'حذاء رياضي كلاسيكي بتصميم أسطوري ومريح', 'High-top legendary sneaker with premium leather upper', 'Nike', 1, $imageFiles['nike_shoes'], 6800.00],
            ['لعبة طائرة درون بالريموت والكاميرا', '4K Camera Quadcopter RC Drone Toy', 'طائرة درون ذكية بكاميرا 4K وتثبيت طيران التلقائي', 'Foldable RC drone with 4K camera, altitude hold and Wi-Fi FPV', 'Sony', 5, $imageFiles['toy_car'], 3200.00],
            ['نظارة شمسية كلاسيكية زارا UV400', 'Zara Classic Aviator UV400 Sunglasses', 'نظارات شمسية طيارين إطار ذهبي وعدسات جودة عالية', 'Classic aviator style sunglasses with metal frame and UV protection', 'Zara', 1, $imageFiles['sunglasses'], 1100.00],
            ['حقيبة ظهر للابتوب جلد طبيعي', 'Premium Genuine Leather Laptop Backpack', 'حقيبة ظهر تتسع للابتوب 15.6 بوصة ومزودة بجيوب تنظيمية', 'Handcrafted genuine leather backpack with padded laptop sleeve', 'Zara', 1, $imageFiles['backpack'], 2400.00],
            ['ساعة سامسونج جالاكسي واتش 6 كلاسيك', 'Samsung Galaxy Watch 6 Classic 47mm', 'ساعة ذكية مع إطار يدور ميكانيكي واستشعار ضغط الدم والقلب', 'Smartwatch featuring rotating bezel, ECG and advanced sleep coaching', 'Samsung', 4, $imageFiles['smartwatch'], 11500.00],
            ['ماكينة حلاقة فيليبس سيريس 9000', 'Philips Shaver Series 9000 Wet & Dry', 'ماكينة حلاقة للوجه بتقنية الشفرات المزدوجة وحلاقة رطبة وجافة', 'Rotary electric shaver with SkinIQ technology for personal comfort', 'Sony', 0, $imageFiles['gadget'], 6300.00],
            ['طقم أواني طهي سيراميك 10 قطع', 'Non-Stick Ceramic Cookware Set 10-Pcs', 'طقم حلل طهي صحية خالية من PFOA وسهلة التنظيف', '10-Piece non-toxic ceramic coated pots and pans cookware set', 'Zara', 2, $imageFiles['cookware'], 5100.00],
            ['موزع عطر وفواحة ذكية بالزيوت', 'Smart Essential Oil Aroma Diffuser', 'فواحة ذكية بإضاءة LED ملونة وتقنية التراسونيك الهادئة', 'Ultrasonic quiet aroma therapy humidifier with color ambient lights', 'Zara', 3, $imageFiles['perfume'], 1250.00],
            ['تيشيرت نايكي دراي فت رياضي', 'Nike Dri-FIT Men Athletic T-Shirt', 'تيشيرت خفيف يمتص العرق ومناسب لتمارين الجيم', 'Moisture-wicking athletic short sleeve training t-shirt', 'Nike', 1, $imageFiles['fashion'], 1350.00],
            ['لعبة روبوت تفاعلي ذكي للأطفال', 'Interactive AI Smart Robot Toy for Kids', 'روبوت ذكي يتكلم ويلعب ويتحكم به بالحركة والصوت', 'Programmable voice-controlled dancing and talking toy robot', 'Sony', 5, $imageFiles['toy_car'], 2100.00],
            ['مكبر صوت سوني لاسلكي بأس قوي', 'Sony SRS-XG300 Portable Bluetooth Speaker', 'سماعة محمولة مع إضاءة حركية وصوت Bass متفجر', 'High-power portable wireless speaker with retractable handle', 'Sony', 0, $imageFiles['speaker'], 8400.00],
            ['آيباد اير الجيل الخامس 64 جيجا', 'Apple iPad Air 5th Gen 64GB Wi-Fi', 'آيباد اير بشريحة M1 وشاشة ليكويد رتينا 10.9 بوصة', 'Powerful iPad featuring M1 chip, Ultra Wide front camera with Center Stage', 'Apple', 0, $imageFiles['macbook'], 24000.00],
            ['فستان سهرة أنيق زارا كوليكشن', 'Zara Evening Elegance Satin Dress', 'فستان سهرة حريمي من الساتان الناعم بتصميم رائع', 'Fluid satin midi dress with cowl neckline and elegant fit', 'Zara', 1, $imageFiles['fashion'], 3600.00],
            ['طقم سكاكين مطبخ يابانية 6 قطع', 'Japanese Stainless Steel Kitchen Knife Set', 'سكاكين مطبخ حادة مصنعة من الستانلس الياباني الصلب', '6-Piece high carbon stainless steel razor sharp kitchen knife block set', 'Sony', 2, $imageFiles['cookware'], 2800.00],
            ['عطر ديور سوڤاج او دي بارفان 100مل', 'Dior Sauvage Eau De Parfum 100ml', 'عطر رجالي أيقوني بنفحات العود والبرغموت الساحرة', 'Powerful raw and noble woody aromatic fragrance for men', 'Zara', 3, $imageFiles['perfume'], 5100.00],
            ['ساعة رجالية كلاسيكية سيراميك', 'Men Executive Ceramic Quartz Watch', 'ساعة يد أنيقة بتقويم وميناء ضد الخدش', 'Luxury quartz watch with scratch-resistant sapphire crystal glass', 'Rolex', 4, $imageFiles['rolex_watch'], 4200.00],
            ['شاحن انكر لاسلكي سريع 3 في 1', 'Anker 3-in-1 Fast Wireless Charging Station', 'قاعدة شحن شاحن سريع للهاتف والساعة والسماعة معاً', '3-in-1 Foldable wireless charging stand for phone, watch and earbuds', 'Apple', 0, $imageFiles['gadget'], 2900.00],
            ['خلاط مولينكس سوبر دوبر 1.75 لتر', 'Moulinex Super Blender with Grinder', 'خلاط مطبخ قوي 500 واط مع مطحنتين للبهارات والمكسرات', '500W durable counter blender with two grinder accessories', 'Sony', 2, $imageFiles['cookware'], 2450.00],
            ['بنطلون شينو كاجوال زارا رجالي', 'Zara Men Casual Chino Trousers', 'بنطلون قطن سليم فيت مريح ومناسب للعمل والمناسبات', 'Comfort stretch cotton slim fit casual chino trousers', 'Zara', 1, $imageFiles['fashion'], 1850.00],
            ['حذاء نسائي أنيق كعب عالي زارا', 'Zara Women Elegant High Heel Pumps', 'حذاء كعب عالي بتصميم أنيق ومريح في الارتداء', 'Classic pointed toe stiletto high heel pumps for formal occasions', 'Zara', 1, $imageFiles['nike_shoes'], 2950.00],
            ['لعبة سيارة كهربائية للأطفال تركب', 'Kids Ride-On Electric Sports Car 12V', 'سيارة كهربائية للأطفال بالريموت ومحركين وسماعة موسيقى', '12V Battery powered ride-on car with remote control and MP3 player', 'Sony', 5, $imageFiles['toy_car'], 7800.00],
            ['كاميرات مراقبة يوفي بدون أسلاك', 'Eufy Cam 2C Wireless Security System', 'كاميرتين مراقبة HD بطارية 180 يوم بدون اشتراك ماهري', 'Wire-free HD security camera set with 180-day battery life', 'Sony', 0, $imageFiles['gadget'], 9200.00],
            ['طقم ملايات سرير مطرزة 5 قطع', 'Embroidered Luxury Bedding Set 5-Pcs', 'طقم سرير قطن مطرز ناعم ووان دافئة', '5-Piece embroidered satin cotton luxury double bed sheet set', 'Zara', 2, $imageFiles['cookware'], 2300.00],
            ['مفرمة لحم وخضار كهربائية 500 واط', 'Electric Food Chopper & Meat Grinder', 'مفرمة مطبخ وعاء استانلس 2 لتر 4 شفرات حادة', '500W Stainless steel bowl food processor and meat grinder', 'Samsung', 2, $imageFiles['cookware'], 1650.00],
            ['سماعة جي بي ال فليب 6 ضد الماء', 'JBL Flip 6 Portable Waterproof Speaker', 'سماعة بلوتوث محمولة بصوت دقيق ضد الماء والأتربة', 'Powerful 2-way speaker system IP67 waterproof Bluetooth speaker', 'Sony', 0, $imageFiles['speaker'], 4800.00],
            ['مكواه بخار عمودية للملابس 1800 واط', 'Garment Steamer Vertical Fabric Steamer', 'مكواة بخارية سريعة التسخين لإزالة التجاعيد بسهولة', 'Full-size garment steamer with removable water tank and hanger', 'Samsung', 2, $imageFiles['cookware'], 3100.00],
            ['حقيبة سفر كابينة 20 بوصة فايبر', 'Fiber Spinner Carry-On Luggage 20"', 'حقيبة سفر خفيفة وقوية بقفل أمان رقمي عجلات 360 درجة', '20-Inch hard shell spinner carry-on luggage with TSA lock', 'Zara', 1, $imageFiles['backpack'], 2600.00],
            ['مستحضر سيروم هيلارونيك أسيد للبشرة', 'Hyaluronic Acid Face Hydrating Serum', 'سيروم ترطيب مكثف ونضارة الفيتامينات للبشرة', 'Deeply hydrating face serum with pure hyaluronic acid and B5', 'Zara', 3, $imageFiles['perfume'], 850.00],
            ['طقم بيجامة قطن ناعم زارا نسائي', 'Zara Women Soft Cotton Pyjama Set', 'طقم بيجامة نوم مريحة وناعمة جداً قطن 100%', '2-Piece comfortable pure cotton loungewear pyjama sleep set', 'Zara', 1, $imageFiles['fashion'], 1750.00],
            ['محفظة جلد طبيعي رجالي حماية RFID', 'Genuine Leather Men Wallet RFID Protection', 'محفظة رجالية جلد طبيعي بجيوب متعددة وحماية البطاقات', 'Bifold genuine leather wallet with RFID blocking security', 'Zara', 1, $imageFiles['backpack'], 750.00],
            ['لعبة منزل الأحلام للأطفال مع اثاث', 'Dream Dollhouse Play Set with Furniture', 'منزل العاب أطفال ملون بقطع أثاث وشخصيات تفاعلية', '3-Story miniature dream house play set with accessories', 'Sony', 5, $imageFiles['toy_car'], 1950.00],
            ['باور بنك مغناطيسي انكر ماج سيف', 'Anker MagGo Magnetic Wireless Power Bank', 'شاحن مغناطيسي سلكي ولاسلكي لهواتف الآيفون', '5000mAh Magnetic snap-on wireless portable power bank', 'Apple', 0, $imageFiles['gadget'], 2200.00],
            ['ميزان مطبخ ديجيتال دقيق 5 كجم', 'Digital Precision Kitchen Scale 5kg', 'ميزان ديجيتال دقيق للمطبخ وإعداد الوجبات', 'High precision 1g/5kg electronic digital kitchen food scale', 'Samsung', 2, $imageFiles['cookware'], 550.00],
            ['سوار رياضي أنيق من الفولاذ للرجال', 'Men Stainless Steel Cuban Link Bracelet', 'سوار يد رجالي ستانلس ستيل غير قابل لتغير اللون', 'Heavy duty stainless steel curb Cuban link chain bracelet for men', 'Rolex', 4, $imageFiles['rolex_watch'], 950.00],
        ];

        foreach ($uniqueRetailProducts as $r) {
            $allProducts[] = [
                'type' => 'retail',
                'name_ar' => $r[0],
                'name_en' => $r[1],
                'desc_ar' => $r[2],
                'desc_en' => $r[3],
                'brand' => $r[4],
                'cat' => $r[5],
                'img' => $r[6],
                'price' => $r[7],
                'wholesale_price' => $r[7] * 0.8,
                'retail_price' => $r[7],
                'min_qty' => 1,
                'discount' => rand(0, 1) ? rand(5, 25) : null,
                'attributes' => [
                    ['key_en' => 'Condition', 'key_ar' => 'الحالة', 'value_en' => 'New Original', 'value_ar' => 'جديد أصلي'],
                ],
            ];
        }

        // Insert all products
        $wholesaleCount = 0;
        $retailCount = 0;

        foreach ($allProducts as $idx => $item) {
            $brandObj = $createdBrands[$item['brand']] ?? null;
            $catObj = $createdCategories[$item['cat']] ?? null;

            $productData = [
                'name_ar' => $item['name_ar'],
                'name_en' => $item['name_en'],
                'desc_ar' => $item['desc_ar'],
                'desc_en' => $item['desc_en'],
                'brand_id' => $brandObj ? $brandObj->id : null,
                'type' => $item['type'],
            ];

            if ($catObj && Schema::hasColumn('products', 'category_id')) {
                $productData['category_id'] = $catObj->id;
            }
            if (Schema::hasColumn('products', 'price')) {
                $productData['price'] = $item['price'];
            }
            if (Schema::hasColumn('products', 'retail_price')) {
                $productData['retail_price'] = $item['retail_price'];
            }
            if (Schema::hasColumn('products', 'wholesale_price')) {
                $productData['wholesale_price'] = $item['wholesale_price'];
            }
            if (Schema::hasColumn('products', 'min_quantity')) {
                $productData['min_quantity'] = $item['min_qty'];
            }
            if (Schema::hasColumn('products', 'stock')) {
                $productData['stock'] = rand(20, 150);
            }
            if (Schema::hasColumn('products', 'main_image')) {
                $productData['main_image'] = $item['img'];
            }
            if (Schema::hasColumn('products', 'images')) {
                $productData['images'] = [$item['img']];
            }

            $product = Product::create($productData);

            if ($catObj && Schema::hasTable('category_product')) {
                $product->categories()->attach($catObj->id);
            }

            // Create Property (Variant)
            $propData = [
                'product_id' => $product->id,
                'main_image' => $item['img'],
                'images' => [$item['img']],
                'min_quantity' => $item['min_qty'],
                'stock' => rand(20, 150),
            ];

            if (Schema::hasColumn('properties', 'price')) {
                $propData['price'] = $item['price'];
            }
            if (Schema::hasColumn('properties', 'wholesale_price')) {
                $propData['wholesale_price'] = $item['wholesale_price'];
            }
            if (Schema::hasColumn('properties', 'retail_price')) {
                $propData['retail_price'] = $item['retail_price'];
            }

            $property = Property::create($propData);

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
            if (!empty($item['discount'])) {
                $offerData = [
                    'property_id' => $property->id,
                    'start' => now()->subDays(2),
                    'end' => now()->addDays(20),
                ];

                if (Schema::hasColumn('offers', 'discount_retail')) {
                    $offerData['discount_retail'] = $item['discount'];
                }
                if (Schema::hasColumn('offers', 'discount_wholesale')) {
                    $offerData['discount_wholesale'] = $item['discount'];
                }
                if (Schema::hasColumn('offers', 'discount_price')) {
                    $offerData['discount_price'] = $item['discount'];
                }
                if (Schema::hasColumn('offers', 'disscount_price')) {
                    $offerData['disscount_price'] = $item['discount'];
                }

                Offer::create($offerData);
            }

            if ($item['type'] === 'wholesale') {
                $wholesaleCount++;
            } else {
                $retailCount++;
            }
        }

        $this->command->info("\n========================================================");
        $this->command->info("🎉 SEEDING COMPLETED SUCCESSFULLY!");
        $this->command->info("Total Products Created: " . ($wholesaleCount + $retailCount));
        $this->command->info("Wholesale Products ('wholesale'): {$wholesaleCount}");
        $this->command->info("Retail Products ('retail'): {$retailCount}");
        $this->command->info("Shared Products (Identical name/image/specs, different wholesale vs retail price): 6 Pairs");
        $this->command->info("========================================================\n");
    }
}
