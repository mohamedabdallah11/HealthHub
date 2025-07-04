<?php

namespace App\Http\Resources\E_commerce;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float)$this->price,
            'stock' => $this->stock,
            'image_url' => $this->image ? asset('storage/products/products/' . $this->image) : null,
            'category' => new CategoryResource($this->category),
        ];
    }
}
