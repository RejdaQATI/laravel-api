<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     title="categories",
 *     description="categories model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="The ID of the categories"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The title of the categories"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="The description of the categories"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="The creation date of the categories"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="The last update date of the categories"
 *     )
 * )
 */
class categories extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
    ];

    /**
     * @OA\Property(
     *     property="products",
     *     type="array",
     *     @OA\Items(ref="#/components/schemas/Product"),
     *     description="The products associated with this category"
     * )
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}


