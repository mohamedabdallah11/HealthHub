<?php

namespace App\Http\Controllers\E_commerce;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Http\Resources\E_commerce\OrderResource;
use App\Http\Requests\E_commerce\OrderRequest;
use App\Http\Resources\E_commerce\OrderCollection;
use Illuminate\Http\Response;

class OrderController extends Controller
{

    public function index(Request $request)
    {

        // $orders = Order::all();
        // return OrderResource::collection($orders);
        $request->validate([
            'status' => 'sometimes|in:pending,completed,cancelled',
            'user_id' => 'sometimes|exists:users,id'
        ]);

        $orders = Order::when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->user_id, fn($q, $userId) => $q->where('user_id', $userId))
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return new OrderCollection($orders);
    }

    public function store(OrderRequest $request)
    {
        // 1. Validate product IDs and fetch products in bulk
        $productIds = collect($request->products)->pluck('id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // 2. Calculate total price
        $total = 0;
        foreach ($request->products as $item) {
            $product = $products[$item['id']];
            $total += $product->price * $item['quantity'];
        }

        // 3. Create the order
        $order = Order::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'total_price' => $total,
            'address' => $request->address,
            'phone' => $request->phone,

        ]);

        // 4. Create order items (using server-side prices)
        foreach ($request->products as $item) {
            $product = $products[$item['id']];

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id, // or $item['id']
                'quantity' => $item['quantity'],
                'price' => $product->price, // Critical: Use DB price, not client-sent
                'created_at' => now(),      // Optional: Add if timestamps are needed
                'updated_at' => now(),
            ]);
        }

        return new OrderResource($order->load('items.product'));
    }


    public function show(Order $order)
    {
        $order->load(['items.product']); // Explicitly load relationships
        return new OrderResource($order);
    }

    public function update(OrderRequest $request, Order $order)
    {

        $order->update($request->validated());
        return new OrderResource($order);
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json([
            'message' => 'Order deleted successfully.'
        ]);
    }

    public function orderHistory(Request $request)
    {

        // $orders = auth()->user()->orders()
        //     ->with(['items.product' => function ($q) {
        //         $q->select('id', 'name', 'price');
        //     }])
        //     ->latest()
        //     ->paginate($request->input('per_page', 5));

        // return new OrderCollection($orders);
        $request->validate([
            'status' => 'sometimes|in:pending,completed,cancelled'
        ]);

        $orders = Order::where('user_id', auth()->id())
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return new OrderCollection($orders);
    }
    public function updateStatus(Request $request, Order $order)
    {
        $validStatuses = [
            'pending' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        $request->validate(['status' => 'required|in:pending,completed,cancelled']);

        // Check if transition is allowed
        if (!in_array($request->status, $validStatuses[$order->status])) {
            return response()->json(
                ['message' => "Cannot change status from {$order->status} to {$request->status}"],
                422
            );
        }

        $order->update(['status' => $request->status]);
        return new OrderResource($order);
    }
}
