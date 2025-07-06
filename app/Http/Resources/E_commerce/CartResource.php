<?php

namespace App\Http\Resources\E_commerce;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_name' => $this->product->name,
            'product_price' => (float)$this->product->price,
            'product_image' => $this->product->image ? asset('storage/products/products/' . $this->product->image) : null,
            'quantity' => $this->quantity,
            'subtotal' => (float)($this->product->price * $this->quantity),
            'added_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
