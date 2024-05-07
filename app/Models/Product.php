<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="Product",
 *     description="Product model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="The ID of the product"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The title of the product"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The description of the product"
 *     ),
 *     @OA\Property(
 *         property="price",
 *         type="number",
 *         format="float",
 *         description="The price of the product"
 *     ),
 *     @OA\Property(
 *         property="stock",
 *         type="integer",
 *         description="The stock quantity of the product"
 *     ),
 *     @OA\Property(
 *         property="image",
 *         type="string",
 *         description="The URL of the product image"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="The creation date of the product"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="The last update date of the product"
 *     ),
 *     @OA\Property(
 *         property="categories",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/categories"),
 *         description="The categories associated with this product"
 *     )
 * )
 */
class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title',
        'description',
        'price',
        'stock',
        'image'
    ];
    
    /**
     * Get the categories associated with this product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(categories::class);
    }
}
