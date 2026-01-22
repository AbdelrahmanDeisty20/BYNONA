<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = [
        "user_id",
        "status",
        "total",
        "type",
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->belongsToMany(Property::class, 'cart_items')->withPivot("quantity","unit_price","line_total","sale_type")->withTimestamps();
    }
    public function settings()
    {
        return $this->hasMany(Setting::class);
    }
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}