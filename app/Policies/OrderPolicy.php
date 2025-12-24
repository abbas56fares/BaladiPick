<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine if user can view any orders
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if user can view the order
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isShop() && $order->shop && $order->shop->user_id === $user->id) {
            return true;
        }

        if ($user->isDelivery() && $order->delivery_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if user can create orders
     */
    public function create(User $user): bool
    {
        return $user->isShop() && $user->shop !== null;
    }

    /**
     * Determine if user can update the order
     */
    public function update(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isShop() && $order->shop && $order->shop->user_id === $user->id) {
            return !in_array($order->status, ['delivered', 'cancelled']);
        }

        if ($user->isDelivery() && $order->delivery_id === $user->id) {
            return !in_array($order->status, ['delivered', 'cancelled']);
        }

        return false;
    }

    /**
     * Determine if user can delete the order
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if user can accept the order (delivery only)
     */
    public function accept(User $user, Order $order): bool
    {
        return $user->isDelivery() && $order->status === 'available';
    }
}
