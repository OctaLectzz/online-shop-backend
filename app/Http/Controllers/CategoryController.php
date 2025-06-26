<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::latest()->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     */
    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Category::generateUniqueSlug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = Category::uploadImage($request->file('image'), $data['name']);
        }

        $category = Category::create($data);

        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     * @param \Illuminate\Http\Request $request
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        if ($category->name !== $data['name']) {
            $data['slug'] = Category::generateUniqueSlug($data['name'], $category->category_id);
        }

        if ($request->hasFile('image')) {
            $category->deleteImage();
            $data['image'] = Category::uploadImage($request->file('image'), $data['name']);
        }

        $category->update($data);

        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->deleteImage();
        $category->delete();

        return response()->json(['message' => 'Category deleted.']);
    }
}
