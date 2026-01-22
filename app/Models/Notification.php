<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    /**
     * الحقول القابلة للـ Mass Assignment
     */
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'type',
        'data',
        'is_read'
    ];

    /**
     * الحقول التي يجب تحويلها تلقائيًا من/إلى JSON
     */
    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();
    }
}
