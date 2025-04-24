<?php

namespace App\Http\Resources\E_commerce;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'total_price' => (float)$this->total_price,
            'status' => $this->status,
            'address' => $this->address,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'products' => $this->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'name' => $item->product->name ?? 'Deleted Product',
                    'quantity' => $item->quantity,
                    'price_per_unit' => (float)$item->price,
                    'subtotal' => (float)($item->price * $item->quantity)
                ];
            })
        ];
    }
}
