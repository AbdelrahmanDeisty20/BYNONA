<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    protected $fillable = ['attribute_definition_id', 'value_en', 'value_ar'];

    public function definition()
    {
        return $this->belongsTo(AttributeDefinition::class);
    }
}
