<?php

namespace App\Http\Controllers\E_commerce;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\Request;
use App\Http\Resources\E_commerce\ProductResource;
use App\Http\Requests\E_commerce\ProductRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return ProductResource::collection($products);
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();

        try {
            if ($request->hasFile('image')) {
                $data['image'] = ImageService::store(
                    $request->file('image'),
                    'product_images',
                    800,
                    800
                );
            }

            $product = Product::create($data);
            return new ProductResource($product);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Image upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255|unique:products,name,' . $product->id,
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'nullable|string'
        ]);

        $data = array_filter($validatedData, function ($value) {
            return $value !== null;
        });

        if ($request->hasFile('image')) {
            if ($product->image) {
                ImageService::delete($product->image, 'product_images');
            }

            $data['image'] = ImageService::store(
                $request->file('image'),
                'product_images',
                800,
                800
            );
        }

        $product->update($data);
        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        ImageService::delete($product->image, 'product_images');
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.'
        ]);
    }
}
