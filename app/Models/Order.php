<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $shop_id
 * @property int|null $delivery_id
 * @property string $client_name
 * @property string $client_phone
 * @property string $client_lat
 * @property string $client_lng
 * @property string $shop_lat
 * @property string $shop_lng
 * @property string $vehicle_type
 * @property string $profit
 * @property string $status
 * @property string|null $qr_code
 * @property bool $qr_verified
 * @property \Carbon\Carbon|null $qr_verified_at
 * @property string|null $delivery_otp
 * @property bool $delivery_verified
 * @property \Carbon\Carbon|null $delivery_verified_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
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
        'order_contents',
        'order_price',
        'distance_km',
        'vehicle_type',
        'delivery_cost',
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
        'order_price' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'delivery_cost' => 'decimal:2',
        'profit' => 'decimal:2',
        'qr_verified' => 'boolean',
        'qr_verified_at' => 'datetime',
        'delivery_verified' => 'boolean',
        'delivery_verified_at' => 'datetime',
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
     * Get updated_at in user's timezone
     */
    protected function updatedAt(): \Illuminate\Database\Eloquent\Casts\Attribute
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
     * Get qr_verified_at in user's timezone
     */
    protected function qrVerifiedAt(): \Illuminate\Database\Eloquent\Casts\Attribute
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
     * Get delivery_verified_at in user's timezone
     */
    protected function deliveryVerifiedAt(): \Illuminate\Database\Eloquent\Casts\Attribute
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
