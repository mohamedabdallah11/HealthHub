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
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('sort_by')) {
            $sortField = $request->input('sort_by');
            $sortDirection = $request->input('sort_dir', 'asc');

            if (in_array($sortField, ['price', 'created_at']) && in_array($sortDirection, ['asc', 'desc'])) {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(10);

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
