<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;

class ShopPolicy
{
    /**
     * Determine if user can view any shops
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if user can view the shop
     */
    public function view(User $user, Shop $shop): bool
    {
        return $user->isAdmin() || ($user->isShop() && $shop->user_id === $user->id);
    }

    /**
     * Determine if user can create shops
     */
    public function create(User $user): bool
    {
        return $user->isShop() && $user->shop === null;
    }

    /**
     * Determine if user can update the shop
     */
    public function update(User $user, Shop $shop): bool
    {
        return $user->isAdmin() || ($user->isShop() && $shop->user_id === $user->id);
    }

    /**
     * Determine if user can delete the shop
     */
    public function delete(User $user, Shop $shop): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if user can verify shops
     */
    public function verify(User $user): bool
    {
        return $user->isAdmin();
    }
}
