<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;
    protected $fillable = [
        "key_en",
        "value_en",
        "key_ar",
        "value_ar",
        "property_id"
    ];

    protected $appends = ['key', 'value'];
    protected $hidden = ['key_ar', 'key_en','value_ar','value_en'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function getKeyAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"key_{$locale}"} ?? $this->key_en;
    }

    public function getValueAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"value_{$locale}"} ?? $this->value_en;
    }
}
