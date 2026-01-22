<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['order_number', 'user_id', 'type', 'total_price', 'status', 'user_name', 'user_phone', 'user_address', 'governorate_id', 'notice'];

    public function items()
    {
        return $this
            ->belongsToMany(Property::class, 'orders_items', 'order_id', 'property_id')
            ->withTrashed()
            ->withPivot(['quantity', 'price', 'sale_type'])
            ->withTimestamps();
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }
}
