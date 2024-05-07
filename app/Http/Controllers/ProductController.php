<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
   /**
     * @OA\Get(
     *     path="/products",
     *     summary="Get all products",
     *     description="Retrieve a list of all products",
     *     @OA\Response(response=200, description="A list of products"),
     * )
     */
    public function index()
    {
        $prod = Product::all();
        
        foreach ($prod as $product) {
        $product->category_titles = $product->categories->pluck('title');
        unset($product->categories);
        }
        return response()->json([
            'products' => $prod,
        ]);
     }
     
    
  /**
     * @OA\Post(
     *     path="/products",
     *     summary="Create a new product",
     *     description="Create a new product with the provided details",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "price", "stock"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="stock", type="integer"),
     *             @OA\Property(property="image", type="string", format="binary"),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="integer")),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="The newly created product"),
     *     @OA\Response(response=400, description="Bad request"),
     * )
     */
    public function store(Request $request)
    {
        $categories = null;
    if ($request->has('categories') && !is_array($request->categories)) {
        $categories =json_decode($request->categories, true);
        $request->merge(['categories'=>$categories]);
    }

        $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'string|max:255',
        'price' => 'required',
        'stock' => 'required',
        'image' => 'sometimes',
        'categories' => 'sometimes|array',
    ]);
    
    $product = Product::create($request->all());
    $product->categories()->attach($categories);
    
    $this->StoreImage($product);
    return response()->json([
        'product' => $product
    ]);
}
  

    /**
     * @OA\Put(
     *     path="/products/{id}",
     *     summary="Update a product by ID",
     *     description="Update details of a product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "price", "stock", "categories"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="price", type="number", format="float"),
     *             @OA\Property(property="stock", type="integer"),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="integer")),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Product updated successfully"),
     *     @OA\Response(response=400, description="Bad request"),
     *     @OA\Response(response=404, description="Product not found"),
     * )
     */

  public function update(Request $request, $id)
  {


    if ($request->has('categories') && !is_array($request->categories)) {
        $categories =json_decode($request->categories, true);
        $request->merge(['categories'=>$categories]);
    }
      $request->validate([
          'title' => 'required|max:255',
          'description' => 'required|string',
          'price' => 'required',
          'stock' => 'required',
          'categories' => 'sometimes|array',
 
      ]);
    
 
      $product = Product::find($id);
  
        if (!$product) {
        return response()->json([
            'message' => 'Product not found.'
        ], 404);
      }
  
      $product->update($request->all());
      
      $this->StoreImage($product);
      if ($request->has('categories')) {
          $product->categories()->sync($request->categories);
      }
  
      return response()->json([
          'product' => $product
      ]);
  }

     /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Get a product by ID",
     *     description="Retrieve details of a product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Product details"),
     *     @OA\Response(response=404, description="Product not found"),
     * )
     */

  public function show(string $id)
  {
    $product = Product::find($id);
    $products = Product::with('categories')->find($id);
    
    $categorytitle = $products->categories->pluck('title');
  
        return response()->json([
          'product' => $product,
          'category_title' => $categorytitle
      ]);
  }
  


    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     summary="Delete a product by ID",
     *     description="Delete a product by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Product deleted successfully"),
     *     @OA\Response(response=404, description="Product not found"),
     * )
     */
    public function destroy(string $id)
    {
    $product = Product::find($id);
    $product->delete();
    return response()->json([
        'product' => $product
    ]);
}

/**
     * @OA\Post(
     *     path="/products/image",
     *     summary="Upload an image for a product",
     *     description="Upload an image for a product and associate it with the product",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"image"},
     *                 @OA\Property(
     *                     property="image",
     *                     description="Image file to upload",
     *                     type="file",
     *                     format="binary"
     *                 ),
     *                 example={"image": "example.jpg"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Image uploaded successfully"),
     *     @OA\Response(response=400, description="Bad request"),
     * )
     */
    private function storeImage(Product $product)
    {
        if (request()->hasFile('image')) {
            $originalFilename = request()->file('image')->getClientOriginalName();
            $imagePath = request()->file('image')->storeAs('images', $originalFilename, 'public');
            $product->image = 'storage/' . $imagePath; 
            $product->save();
        }
    }
    
    
}
