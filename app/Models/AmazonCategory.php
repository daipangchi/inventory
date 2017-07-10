<?php

namespace App\Models;

use App\ModelCacher;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AmazonCategory
 *
 * @property integer $id
 * @property integer $cadabra_category_id
 * @property integer $node_id
 * @property string $node_name
 * @property string $node_store_context_name
 * @property string $path_by_id
 * @property string $path_by_name
 * @property string $child_nodes
 * @property string $product_type_definitions
 * @property boolean $node_level
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonCategory whereCadabraCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonCategory whereNodeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonCategory whereNodeName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonCategory whereNodeStoreContextName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonCategory wherePathById($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonCategory wherePathByName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonCategory whereChildNodes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonCategory whereProductTypeDefinitions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonCategory whereNodeLevel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AmazonCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AmazonCategory extends Model
{
    use ModelCacher;

    protected $table = 'amazon_categories';

    protected $fillable = [
        'node_id',
        'node_name',
        'node_store_context_name',
        'path_by_id',
        'path_by_name',
        'child_nodes',
        'product_type_definitions',
        'node_level',
    ];
}
