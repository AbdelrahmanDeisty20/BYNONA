<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'address_ar',
        'address_en',
        'phone',
        'whatsapp',
        'facebook'
    ];

    protected $appends = [
        'address',
    ];

    protected $hidden = [
        'address_ar',
        'address_en',
    ];

    public function getAddressAttribute()
    {
        $locale = app()->getLocale();

        return $this->{"address_{$locale}"} ?? $this->address_en;
    }
}
