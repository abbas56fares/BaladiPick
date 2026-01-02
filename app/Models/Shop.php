<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $shop_name
 * @property string $phone
 * @property string $address
 * @property string $latitude
 * @property string $longitude
 * @property bool $is_verified
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_name',
        'phone',
        'address',
        'latitude',
        'longitude',
        'is_verified',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_verified' => 'boolean',
    ];

    /**
     * Relationship: Shop belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Shop has many Orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
