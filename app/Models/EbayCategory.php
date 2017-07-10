<?php

namespace App\Models;

use App\ModelCacher;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EbayCategory
 *
 * @property integer $id
 * @property integer $cadabra_category_id
 * @property integer $ebay_category_id
 * @property string $ebay_category_name
 * @property integer $ebay_category_parent_id
 * @property boolean $ebay_category_level
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EbayCategory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EbayCategory whereCadabraCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EbayCategory whereEbayCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EbayCategory whereEbayCategoryName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EbayCategory whereEbayCategoryParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EbayCategory whereEbayCategoryLevel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EbayCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EbayCategory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EbayCategory extends Model
{
    use ModelCacher;

    public $table = 'ebay_categories';

    public $fillable = [
        'category_id',
        'ebay_category_id',
        'ebay_category_name',
        'ebay_category_parent_id',
        'ebay_category_level',
    ];
}
