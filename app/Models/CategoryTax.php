<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CategoryTax
 *
 * @property integer $id
 * @property string $country_code
 * @property integer $category_id
 * @property float $percentage
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryTax whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryTax whereCountryCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryTax whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryTax wherePercentage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryTax whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryTax whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CategoryTax extends Model
{
    protected $table = 'categories_taxes';

    protected $fillable = ['country_code', 'category_id', 'percentage'];
}
