<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeDefinition extends Model
{
    use HasFactory;

    protected $fillable = ['name_en', 'name_ar'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
