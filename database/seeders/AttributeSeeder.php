<?php

namespace Database\Seeders;

use App\Models\AttributeDefinition;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            [
                'name_en' => 'Color',
                'name_ar' => 'اللون',
                'values' => [
                    ['en' => 'Red', 'ar' => 'أحمر'],
                    ['en' => 'Black', 'ar' => 'أسود'],
                    ['en' => 'Green', 'ar' => 'أخضر'],
                    ['en' => 'White', 'ar' => 'أبيض'],
                    ['en' => 'Blue', 'ar' => 'أزرق'],
                ]
            ],
            [
                'name_en' => 'Size',
                'name_ar' => 'المقاس',
                'values' => [
                    ['en' => 'Small', 'ar' => 'صغير'],
                    ['en' => 'Medium', 'ar' => 'متوسط'],
                    ['en' => 'Large', 'ar' => 'كبير'],
                    ['en' => 'XL', 'ar' => 'كبير جداً'],
                    ['en' => 'XXL', 'ar' => 'كبير جداً جداً'],
                ]
            ],
            [
                'name_en' => 'Material',
                'name_ar' => 'الخامة',
                'values' => [
                    ['en' => 'Cotton', 'ar' => 'قطن'],
                    ['en' => 'Polyester', 'ar' => 'بوليستر'],
                    ['en' => 'Silk', 'ar' => 'حرير'],
                    ['en' => 'Leather', 'ar' => 'جلد'],
                ]
            ],
            [
                'name_en' => 'Fabric',
                'name_ar' => 'القماش',
                'values' => [
                    ['en' => 'Linen', 'ar' => 'كتان'],
                    ['en' => 'Wool', 'ar' => 'صوف'],
                    ['en' => 'Denim', 'ar' => 'جينز'],
                    ['en' => 'Chiffon', 'ar' => 'شيفون'],
                    ['en' => 'Velvet', 'ar' => 'قطيفة'],
                ]
            ],
            [
                'name_en' => 'Season',
                'name_ar' => 'الموسم',
                'values' => [
                    ['en' => 'Summer', 'ar' => 'صيفي'],
                    ['en' => 'Winter', 'ar' => 'شتوي'],
                    ['en' => 'Spring', 'ar' => 'ربيعي'],
                    ['en' => 'Autumn', 'ar' => 'خريفي'],
                ]
            ],
            [
                'name_en' => 'Style',
                'name_ar' => 'الستايل',
                'values' => [
                    ['en' => 'Casual', 'ar' => 'كاجوال'],
                    ['en' => 'Formal', 'ar' => 'فورمال'],
                    ['en' => 'Sport', 'ar' => 'رياضي'],
                    ['en' => 'Classic', 'ar' => 'كلاسيكي'],
                    ['en' => 'Vintage', 'ar' => 'فينتج'],
                ]
            ],
            [
                'name_en' => 'Pattern',
                'name_ar' => 'النقشة',
                'values' => [
                    ['en' => 'Solid', 'ar' => 'سادة'],
                    ['en' => 'Striped', 'ar' => 'مخطط'],
                    ['en' => 'Checkered', 'ar' => 'كاروهات'],
                    ['en' => 'Printed', 'ar' => 'منقوش'],
                    ['en' => 'Floral', 'ar' => 'مشجر'],
                ]
            ],
            [
                'name_en' => 'Occasion',
                'name_ar' => 'المناسبة',
                'values' => [
                    ['en' => 'Daily', 'ar' => 'يومي'],
                    ['en' => 'Party', 'ar' => 'حفلات'],
                    ['en' => 'Wedding', 'ar' => 'زفاف'],
                    ['en' => 'Work', 'ar' => 'عمل'],
                    ['en' => 'Beach', 'ar' => 'شاطئ'],
                ]
            ],
            [
                'name_en' => 'Fit',
                'name_ar' => 'القالب',
                'values' => [
                    ['en' => 'Slim Fit', 'ar' => 'نحيف'],
                    ['en' => 'Regular Fit', 'ar' => 'عادي'],
                    ['en' => 'Oversized', 'ar' => 'واسع جداً'],
                    ['en' => 'Relaxed', 'ar' => 'مريح'],
                ]
            ],
            [
                'name_en' => 'Sleeve',
                'name_ar' => 'الأكمام',
                'values' => [
                    ['en' => 'Short', 'ar' => 'كم قصير'],
                    ['en' => 'Long', 'ar' => 'كم طويل'],
                    ['en' => 'Sleeveless', 'ar' => 'بدون أكمام'],
                    ['en' => '3/4 Sleeve', 'ar' => 'كم 3/4'],
                ]
            ],
            [
                'name_en' => 'Neckline',
                'name_ar' => 'فتحة الرقبة',
                'values' => [
                    ['en' => 'V-Neck', 'ar' => 'فتحة V'],
                    ['en' => 'Round Neck', 'ar' => 'فتحة مستديرة'],
                    ['en' => 'Turtleneck', 'ar' => 'هاي كول'],
                    ['en' => 'Hooded', 'ar' => 'بغطاء رأس'],
                ]
            ],
            [
                'name_en' => 'Capacity',
                'name_ar' => 'السعة',
                'values' => [
                    ['en' => '5kg', 'ar' => '5 كيلو'],
                    ['en' => '7kg', 'ar' => '7 كيلو'],
                    ['en' => '10kg', 'ar' => '10 كيلو'],
                    ['en' => '12kg', 'ar' => '12 كيلو'],
                    ['en' => '300L', 'ar' => '300 لتر'],
                    ['en' => '400L', 'ar' => '400 لتر'],
                    ['en' => '500L', 'ar' => '500 لتر'],
                ]
            ],
            [
                'name_en' => 'Energy Rating',
                'name_ar' => 'كفاءة الطاقة',
                'values' => [
                    ['en' => 'A+++', 'ar' => 'A+++'],
                    ['en' => 'A++', 'ar' => 'A++'],
                    ['en' => 'A+', 'ar' => 'A+'],
                    ['en' => 'B', 'ar' => 'B'],
                ]
            ],
            [
                'name_en' => 'Type',
                'name_ar' => 'النوع',
                'values' => [
                    ['en' => 'Automatic', 'ar' => 'أوتوماتيك'],
                    ['en' => 'Semi-Automatic', 'ar' => 'نصف أوتوماتيك'],
                    ['en' => 'Front Load', 'ar' => 'فتح أمامية'],
                    ['en' => 'Top Load', 'ar' => 'فتحة علوية'],
                    ['en' => 'Side by Side', 'ar' => 'ثنائ الأبواب'],
                ]
            ],
            [
                'name_en' => 'Finish',
                'name_ar' => 'الطبقة الخارجية',
                'values' => [
                    ['en' => 'Stainless Steel', 'ar' => 'ستانلس ستيل'],
                    ['en' => 'Silver', 'ar' => 'فضي'],
                    ['en' => 'Black Mirror', 'ar' => 'أسود زجاجي'],
                    ['en' => 'White', 'ar' => 'أبيض'],
                ]
            ],
            [
                'name_en' => 'Warranty',
                'name_ar' => 'الضمان',
                'values' => [
                    ['en' => '1 Year', 'ar' => 'سنة واحدة'],
                    ['en' => '2 Years', 'ar' => 'سنتان'],
                    ['en' => '5 Years', 'ar' => '5 سنوات'],
                    ['en' => '10 Years', 'ar' => '10 سنوات'],
                ]
            ],
            [
                'name_en' => 'Power',
                'name_ar' => 'القدرة',
                'values' => [
                    ['en' => '1000W', 'ar' => '1000 واط'],
                    ['en' => '1500W', 'ar' => '1500 واط'],
                    ['en' => '2000W', 'ar' => '2000 واط'],
                    ['en' => '2500W', 'ar' => '2500 واط'],
                ]
            ]
        ];

        foreach ($attributes as $attrData) {
            $definition = AttributeDefinition::updateOrCreate(
                ['name_en' => $attrData['name_en']],
                ['name_ar' => $attrData['name_ar']]
            );

            foreach ($attrData['values'] as $valData) {
                AttributeValue::updateOrCreate(
                    [
                        'attribute_definition_id' => $definition->id,
                        'value_en' => $valData['en']
                    ],
                    [
                        'value_ar' => $valData['ar']
                    ]
                );
            }
        }
    }
}
