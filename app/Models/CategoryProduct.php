<?php

namespace App\Models;

use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

/**
 * App\Models\CategoryProduct
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $product_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Category $category
 * @property-read \App\Models\Products\Product $product
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryProduct whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryProduct whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryProduct whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryProduct whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CategoryProduct extends Model
{
    /**
     * @var string
     */
    protected $table = 'categories_products';

    /**
     * @var array
     */
    protected $fillable = ['category_id', 'product_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Create Category-Product relations.
     *
     * @param $productId
     * @param array $categoryIds
     * @return void
     */
    public static function attach($productId, array $categoryIds)
    {
        $categories = Category::whereIn('category_id', $categoryIds)->get(['path_by_category_id']);

        $ids = [];

        foreach ($categories as $category) {
            $ids = array_merge($ids, explode(',', $category->path_by_category_id));
        }

        foreach (array_unique($ids) as $categoryId) {
            static::create([
                'product_id'  => $productId,
                'category_id' => $categoryId,
            ]);
        }
    }

    /**
     * Tries to create an entry. If integrity constraint violation
     * is thrown (unique error), return the existing row.
     *
     * @param array $attributes
     * @return Model
     */
    public static function create(array $attributes = [])
    {
        try {
            return parent::create($attributes);
        } catch (QueryException $e) {
            // If it's an integrity constraint violation, return
            // the exsting row
            if ($e->getCode() == 23000) {
                $query = new static;

                foreach ($attributes as $column => $value) {
                    $query->where($column, $value);
                }

                return $query->first();
            }
            // If it's a different error, rethrow it.
            else {
                throw $e;
            }
        }
    }
}
