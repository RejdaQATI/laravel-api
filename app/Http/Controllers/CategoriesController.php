<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoriesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/categories",
     *     summary="Get all categories",
     *     @OA\Response(response=200, description="A list of categories"),
     * )
     */
    public function index()
    {
        $categories = Category::with('products')->get();

        foreach ($categories as $category) {
            $category->products_titles = $category->products->pluck('title');
            unset($category->products);
        }

        return response()->json([
            'categories' => $categories
        ]);
    }

    /**
     * @OA\Post(
     *     path="/categories",
     *     summary="Create a new category",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","description"},
     *             @OA\Property(property="name", type="string", example="Category 1"),
     *             @OA\Property(property="description", type="string", example="Description of Category 1"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="The newly created category"),
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'string|max:255',
        ]);

        $category = Category::create($request->all());
        return response()->json([
            'category' => $category
        ]);
    }

    /**
     * @OA\Put(
     *     path="/categories/{id}",
     *     summary="Update a category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the category to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","description"},
     *             @OA\Property(property="name", type="string", example="Updated Category"),
     *             @OA\Property(property="description", type="string", example="Updated Description"),
     *         )
     *     ),
     *     @OA\Response(response=200, description="Updated category"),
     *     @OA\Response(response=404, description="Category not found"),
     * )
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required|max:255',
        ]);

        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found.'
            ], 404);
        }

        $category->update($request->all());

        return response()->json([
            'category' => $category
        ]);
    }

    /**
     * @OA\Get(
     *     path="/categories/{id}",
     *     summary="Get a category by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the category to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Details of the specified category"),
     *     @OA\Response(response=404, description="Category not found"),
     * )
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        $categ = Category::with('products')->find($id);
        $producttitle = $categ->products->pluck('title');
      
        return response()->json([
            'category' => $category,
            'products_title' => $producttitle,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/categories/{id}",
     *     summary="Delete a category by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the category to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Deleted category"),
     *     @OA\Response(response=404, description="Category not found"),
     * )
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        $category->delete();
        return response()->json([
            'category' => $category
        ]);
    }
}
