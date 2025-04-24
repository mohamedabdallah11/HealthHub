<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;

class EcommercePolicy
{
    public function viewAny(User $user)
    {
        return $user->role === 'admin'; // Only Admins can see all orders
    }

    public function view(User $user, Order $order)
    {
        return $user->role === 'admin' || $user->id === $order->user_id; // Admins or order owners
    }

    public function create(User $user)
    {
        return $user->role === 'client'; // Clients can place orders
    }

    public function update(User $user, Order $order)
    {
        return $user->role === 'admin'; // Only Admins can update orders
    }

    public function delete(User $user, Order $order)
    {
        return $user->role === 'admin'; // Only Admins can delete orders
    }

    public function updateStatus(User $user, Order $order)
    {
        return $user->role === 'admin'; // Only Admins can update order status
    }
}
