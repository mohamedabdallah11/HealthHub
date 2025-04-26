<?php

namespace App\Http\Resources\E_commerce;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CartCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total_items' => $this->count(),
                'total_price' => (float)$this->collection->sum(function ($item) {
                    return $item->product->price * $item->quantity;
                }),
            ],
        ];
    }
}
