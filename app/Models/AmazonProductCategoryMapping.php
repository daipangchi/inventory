<?php

namespace App\Models;

use App\ModelCacher;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AmazonProductCategoryMapping
 *
 * @property integer $id
 * @property string $product_asin
 * @property string $node_ids
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonProductCategoryMapping whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonProductCategoryMapping whereProductAsin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonProductCategoryMapping whereNodeIds($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonProductCategoryMapping whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonProductCategoryMapping whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AmazonProductCategoryMapping extends Model
{
    use ModelCacher;

    protected $table = 'amazon_product_category_mappings';

    protected $fillable = [
        'product_asin',
        'node_ids',
    ];
}
