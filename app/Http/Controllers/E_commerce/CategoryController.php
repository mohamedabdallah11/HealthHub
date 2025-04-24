<?php

namespace App\Http\Controllers\E_commerce;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\E_commerce\CategoryResource;
use App\Http\Requests\E_commerce\CategoryRequest;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }

    public function store(CategoryRequest $request)
    {

        $category = Category::create($request->validated());
        return new CategoryResource($category);
    }

    public function show(Category $category)
    {
        $category->load('products'); // Eager load products

        return response()->json([
            'category' => $category
        ]);
    }

    public function update(CategoryRequest $request, Category $category)
    {

        $category->update($request->validated());
        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {

        $category->delete();
        return response()->json([
            'message' => 'Category deleted successfully.'
        ]);
    }
}
