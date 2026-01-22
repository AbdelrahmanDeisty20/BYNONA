<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyValue extends Model
{
    use HasFactory;
    protected $fillable = [
        'property_id',
        'value_en',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductProperty::class, 'property_value_id');
    }
}