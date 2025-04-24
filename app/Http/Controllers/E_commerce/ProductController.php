<?php

namespace App\Http\Controllers\E_commerce;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\E_commerce\ProductResource;
use App\Http\Requests\E_commerce\ProductRequest;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return ProductResource::collection($products);
    }

    public function store(ProductRequest $request)
    {

        $product = Product::create($request->validated());
        return new ProductResource($product);
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(ProductRequest $request, Product $product)
    {

        $product->update($request->validated());
        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {

        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully.'
        ]);
    }
}
