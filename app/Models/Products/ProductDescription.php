<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class ProductDescription extends Model
{
    protected $table = 'products_description';
    
    protected $fillable = [
        'product_id',
        'title'
    ];
}
