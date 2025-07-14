<?php

namespace App\Http\Controllers\E_commerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\E_commerce\CartRequest;
use App\Http\Resources\E_commerce\CartCollection;
use App\Http\Resources\E_commerce\CartResource;
use App\Http\Resources\E_commerce\OrderResource;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = Cart::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return new CartCollection($cartItems);
    }

    public function store(CartRequest $request)
    {
        $existingCartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingCartItem) {
            $existingCartItem->increment('quantity', $request->quantity);
            return new CartResource($existingCartItem->load('product'));
        }

        $cartItem = Cart::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        return new CartResource($cartItem->load('product'));
    }


    public function orderSingleItem(Cart $cartItem)
    {
        if ($cartItem->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'total_price' => $cartItem->product->price * $cartItem->quantity,
            'address' => request('address'), 
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $cartItem->product_id,
            'quantity' => $cartItem->quantity,
            'price' => $cartItem->product->price
        ]);

        $cartItem->delete();

        return new OrderResource($order);
    }

    public function orderSelectedItems(CartRequest $request)
    {
        $validated = $request->validate([
            'cart_items' => 'required|array|min:1',
            'cart_items.*' => [
                'required',
                'integer',
                Rule::exists('cart', 'id')->where('user_id', auth()->id())
            ],
            'address' => 'required|string|max:255'
        ]);
        DB::beginTransaction();
        try {
            $cartItems = Cart::with(['product' => function ($query) {
                $query->lockForUpdate();
            }])
                ->whereIn('id', $validated['cart_items'])
                ->get();

            $total = 0;
            foreach ($cartItems as $item) {
                if ($item->product->stock < $item->quantity) {
                    throw new \Exception(
                        "Insufficient stock for {$item->product->name}. " .
                            "Available: {$item->product->stock}, Requested: {$item->quantity}"
                    );
                }
                $total += $item->product->price * $item->quantity;
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'status' => 'pending',
                'total_price' => $total,
                'address' => $validated['address']
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            Cart::whereIn('id', $validated['cart_items'])->delete();

            DB::commit();

            return new OrderResource($order->load('items.product'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'error_type' => class_basename($e)
            ], 422);
        }
    }

    public function update(CartRequest $request, Cart $cart)
    {

        if ($cart->user_id !== auth()->id()) {
            abort(403, 'You can only update your own cart items');
        }

        $cart->update([
            'quantity' => $request->quantity,
        ]);

        return new CartResource($cart->load('product'));
    }

    public function destroy(Cart $cart)
    {
        abort_unless(
            $cart->user_id === auth()->id(),
            403,
            'Unauthorized action'
        );
        $cart->delete();

        return response()->json([
            'message' => 'Item removed from cart successfully'
        ]);
    }

    public function clear()
    {
        Cart::where('user_id', auth()->id())->delete();

        return response()->json([
            'message' => 'Cart cleared successfully'
        ]);
    }
}
