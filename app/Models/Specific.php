<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specific extends Model
{
    use HasFactory;
    protected $fillable = [
        "key_ar",
        "key_en",
        "value_ar",
        "value_en",
        "product_id"
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}