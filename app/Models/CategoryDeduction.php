<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CategoryDeduction
 *
 * @property integer $id
 * @property integer $category_id
 * @property float $amazon_deduction
 * @property float $ebay_deduction
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryDeduction whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryDeduction whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryDeduction whereAmazonDeduction($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryDeduction whereEbayDeduction($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryDeduction whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CategoryDeduction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CategoryDeduction extends Model
{
    protected $table = 'categories_deductions';

    protected $fillable = ['category_id', 'amazon_deduction', 'ebay_deduction'];
}
