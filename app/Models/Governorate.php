<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    use HasFactory;
    protected $fillable= [
        "name_ar",
        "name_en"
        ];
          protected $appends = ['name'];

    protected $hidden = ['name_ar', 'name_en'];
        public function getNameAttribute()
        {
           $locale = app()->getLocale();

        return $this->{"name_{$locale}"} ?? $this->name_en;
        }
        public function orders()
        {
            return $this->hasMany(Order::class);
        }
        public function settings()
        {
            return $this->hasMany(Setting::class);
        }
        
}
