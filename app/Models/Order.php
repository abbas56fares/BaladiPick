<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id',
        'delivery_id',
        'client_name',
        'client_phone',
        'client_lat',
        'client_lng',
        'shop_lat',
        'shop_lng',
        'vehicle_type',
        'profit',
        'status',
        'qr_code',
        'qr_verified',
        'qr_verified_at',
        'delivery_otp',
        'delivery_verified',
        'delivery_verified_at',
    ];

    protected $casts = [
        'client_lat' => 'decimal:8',
        'client_lng' => 'decimal:8',
        'shop_lat' => 'decimal:8',
        'shop_lng' => 'decimal:8',
        'profit' => 'decimal:2',
        'qr_verified' => 'boolean',
        'qr_verified_at' => 'datetime',
        'delivery_verified' => 'boolean',
        'delivery_verified_at' => 'datetime',
    ];

    /**
     * Relationship: Order belongs to Shop
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Relationship: Order belongs to Delivery User
     */
    public function delivery()
    {
        return $this->belongsTo(User::class, 'delivery_id');
    }

    /**
     * Relationship: Order has one Payment
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Relationship: Order has many Logs
     */
    public function logs()
    {
        return $this->hasMany(OrderLog::class);
    }
}
