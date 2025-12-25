<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'verified',
        'cancellation_count',
        'cooldown_all_until',
        'id_document_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verified' => 'boolean',
            'cooldown_all_until' => 'datetime',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is shop
     */
    public function isShop(): bool
    {
        return $this->role === 'shop';
    }

    /**
     * Check if user is delivery
     */
    public function isDelivery(): bool
    {
        return $this->role === 'delivery';
    }

    /**
     * Relationship: User has one Shop
     */
    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    /**
     * Relationship: Delivery user has many orders
     */
    public function deliveryOrders()
    {
        return $this->hasMany(Order::class, 'delivery_id');
    }

    /**
     * Relationship: User sends notifications
     */
    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, 'sender_id');
    }

    /**
     * Relationship: User receives notifications
     */
    public function receivedNotifications()
    {
        return $this->hasMany(Notification::class, 'receiver_id');
    }
}
