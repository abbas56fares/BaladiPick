<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'changed_by',
        'status',
        'note',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get created_at in user's timezone
     */
    protected function createdAt(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function ($value) {
                if (!$value) return null;
                $carbon = \Carbon\Carbon::parse($value);
                if (auth()->check() && auth()->user()->timezone) {
                    return $carbon->timezone(auth()->user()->timezone);
                }
                return $carbon;
            }
        );
    }


    /**
     * Relationship: Log belongs to Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship: Log belongs to User (changed_by)
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
