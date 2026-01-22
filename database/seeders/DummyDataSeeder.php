<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Offer;
use App\Models\Product;
use App\Models\Property;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Clear all existing offers first to start clean
        Offer::truncate();

        // 1. Generate 15 Offers with specific discount amounts
        $this->command->info('Seeding 15 product offers with discount amounts (5, 10, 15, 20)...');
        $properties = Property::inRandomOrder()->limit(15)->get();

        foreach ($properties as $prop) {
            $discountAmount = [5, 10, 15, 20][array_rand([5, 10, 15, 20])];

            Offer::create([
                'property_id' => $prop->id,
                'start' => now(),
                'end' => now()->addDays(3),
                'discount_retail' => $prop->retail_price ? $discountAmount : null,
                'discount_wholesale' => $prop->wholesale_price ? $discountAmount : null,
            ]);
            $this->command->info("Added discount amount: $discountAmount to property ID: " . $prop->id);
        }
    }
}
