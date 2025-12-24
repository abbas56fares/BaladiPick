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
