<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductProperty extends Model
{
    use HasFactory;
    protected $table = 'product_property';

    protected $fillable = [
        'product_id',
        'property_id',
        'property_value_id',
        'stock',
    ];

    /* ===== العلاقات ===== */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function value()
    {
        return $this->belongsTo(PropertyValue::class, 'property_value_id');
    }

    /* ===== Helpers ===== */
}