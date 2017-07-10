<?php

namespace App\Models\Products;

use App\Jobs\DownloadProductPhotoJob;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Merchants\Merchant;
use App\Models\Products\ChangeLogDataTypes\UpdatedDataType;
use App\Models\ShippingDeduction;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Image as Intervention;
use Storage;

/**
 * App\Models\Products\Product
 *
 * @property integer $id
 * @property integer $merchant_id
 * @property integer $parent_id
 * @property string $sku
 * @property string $parent_sku
 * @property string $type
 * @property string $name
 * @property string $description
 * @property string $brand
 * @property string $manufacturer
 * @property string $model_number
 * @property string $condition
 * @property string $features
 * @property integer $quantity
 * @property string $channel
 * @property integer $variants
 * @property string $amazon_asin
 * @property string $ebay_id
 * @property string $product_identifier
 * @property string $product_identifier_type
 * @property float $price
 * @property float $msrp
 * @property string $barcode
 * @property float $weight
 * @property string $weight_unit
 * @property integer $length
 * @property integer $width
 * @property integer $height
 * @property string $dimensions_unit
 * @property string $specs
 * @property string $variations
 * @property string $attributes
 * @property string $status
 * @property boolean $is_published
 * @property string $exported_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Products\Product $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Product[] $children
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $categories
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Image[] $images
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\ChangeLog[] $changeLogs
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereMerchantId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereSku($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereParentSku($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereBrand($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereManufacturer($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereModelNumber($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereCondition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereFeatures($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereChannel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereVariants($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereAmazonAsin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereEbayId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereProductIdentifier($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereProductIdentifierType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereMsrp($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereBarcode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereWeight($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereWeightUnit($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereLength($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereWidth($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereHeight($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereDimensionsUnit($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereSpecs($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereVariations($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereAttributes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereIsPublished($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereExportedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Products\Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    const STATUS_PROCESSING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_PENDING = 3;
    const STATUS_PENDING_NO_CATEGORY = 3;
    const STATUS_PENDING_NO_WEIGHT = 4;    
    const STATUS_REMOVED = 5;
    const STATUS_DISABLED = 6;
    
    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    //protected $appends = ['description'];

    /**
     * Please, use only this products types for 'type' field
     */
    public $availableTypes = [
        'simple',
        'bundle',
        'configurable',
    ];
    
    public $categoryFee = 0;

    const TYPE_SIMPLE = 'simple';
    const TYPE_BUNDLE = 'bundle';
    const TYPE_CONFIGURABLE = 'configurable';

    /**
     * @param $specs
     */
    public function setSpecsAttribute($specs)
    {
        if (is_array($specs)) {
            $this->attributes['specs'] = \Guzzlehttp\json_encode($specs);
        }
    }

    /**
     * @param $specs
     * @return mixed
     */
    public function getSpecsAttribute($specs)
    {
        if (is_string($specs) && $specs) {
            return \Guzzlehttp\json_decode($specs, true);
        }
    }

    /**
     * @param $variations
     */
    public function setVariationsAttribute($variations)
    {
        if (is_array($variations)) {
            $this->attributes['variations'] = \Guzzlehttp\json_encode($variations);
        }
    }

    /**
     * @param $variations
     * @return mixed
     */
    public function getVariationsAttribute($variations)
    {
        if (is_string($variations) && $variations) {
            return \Guzzlehttp\json_decode($variations, true);
        }
    }

    /**
     * @param $attributes
     */
    public function setAttributesAttribute($attributes)
    {
        if (is_array($attributes)) {
            $this->attributes['attributes'] = \Guzzlehttp\json_encode($attributes);
        }
    }

    /**
     * @param $attributes
     * @return mixed
     */
    public function getAttributesAttribute($attributes)
    {
        if (is_string($attributes) && $attributes) {
            return \Guzzlehttp\json_decode($attributes, true);
        }

        return null;
    }

    /**`
     * @param $features
     * @return string
     */
    public function setFeaturesAttribute($features)
    {
        if (is_array($features)) {
            $this->attributes['features'] = json_encode($features);
        }
    }

    /**
     * @param $features
     * @return string
     */
    public function getFeaturesAttribute($features)
    {
        return json_decode($features, true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /**
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /**
     * @return mixed
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'categories_products');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function changeLogs()
    {
        return $this->hasMany(ChangeLog::class, 'product_id');
    }
    
    public function priceDeductions()
    {
        return PriceDeductions::where('product_id', $this->id)->get();
        //return $this->hasMany(PriceDeductions::class, 'product_id');
    }

    /**
     * If a product has parent_id, it is not a parent.
     *
     * @return bool
     */
    public function isParent()
    {
        return $this->attributes['parent_id'] ? false : true;
    }

    /**
     * @param Merchant $merchant
     * @return bool
     */
    public function isOwnedBy(Merchant $merchant)
    {
        return $this->merchant_id === $merchant->id;
    }

    /**
     * @param array $urls
     * @return $this
     */
    public function downloadAndAttachImages(array $urls)
    {   
        if(count($urls) > 0) {
            $newUrls = [];
            foreach($urls as $url) {
                $row = $this->images->where('original_url', $url)->first();
                if(isset($row->id)) {
                    continue;
                }
                
                $newUrls[] = $url;
            }
            
            if(count($newUrls) > 0) {
                $job = new DownloadProductPhotoJob(
                    $this->merchant_id,
                    $this->id,
                    $this->sku,
                    $newUrls
                );
                dispatch($job);
            }
        }                      
        
        return $this;
    }

    /**
     * @param array $categoryIds
     * @return $this
     */
    public function attachCategories(array $categoryIds)
    {
        // remove existing categories
        // added by andrey
        //CategoryProduct::where('product_id', $this->id)->delete();
        // ended by andrey
        
        $categories = Category::cache()->whereInLoose('category_id', $categoryIds);
        $categoryIds = [];

        // Get the full path to the category (all the parents).
        foreach ($categories as $category) {
            $categoryIds = array_merge($categoryIds, explode(',', $category->path_by_category_id));
            
            if($this->categoryFee <= $category->fee) {
                $this->categoryFee = $category->fee;
            }
        }

        foreach ($categoryIds as $categoryId) {
            CategoryProduct::create([
                'product_id'  => $this->id,
                'category_id' => $categoryId,
            ]);
        }

        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function mergeChildAttributes(array $attributes)
    {
        $variations = $this->variations;

        foreach ($attributes as $name => $value) {
            if (isset($variations[$name])) {
                $variations[$name][] = $value;
            }
            else {
                $variations[$name] = [$value];
            }

            $variations[$name] = array_unique($variations[$name]);
        }

        // Just to make sure that the parent type is set to configurable.
        $this->type = Product::TYPE_CONFIGURABLE;

        $this->variations = $variations;

        $this->variants = $this->variants + 1;

        return $this;
    }

    /**
     * @return Category|null
     */
    public function getDeepestCategory()
    {
        $maxLevel = Category::getMaxCategoryLevel();

        while ($maxLevel) {
            $category = $this->categories->where('level', $maxLevel)->first();

            if ($category) {
                return $category;
            }

            $maxLevel--;
        }

        return null;
    }
    
    public function getStatusResult() 
    {
        $result = array(
            'label' => '',
            'error' => '',
            'actives' => 2
        );
        if($this->status == Product::STATUS_ACTIVE) {
            $result['label'] = 'Active';
        } else if($this->status == Product::STATUS_PUBLISHED) {
            $result['label'] = 'Published';
            $result['actives'] = 3;
        } else if($this->status == Product::STATUS_PENDING_NO_CATEGORY) {
            $result['label'] = 'Pending';
            $result['error'] = 'There is no cateogry.';
            $result['actives'] = 0;
        } else if($this->status == Product::STATUS_PENDING_NO_WEIGHT) {
            $result['label'] = 'Pending';
            $result['error'] = 'There is no weight.';
            $result['actives'] = 0;
        } else if($this->status == Product::STATUS_REMOVED) {
            $result['label'] = 'Removed';
            $result['error'] = 'This product is removed.';
            $result['actives'] = 0;
        } else if($this->status == Product::STATUS_DISABLED) {
            $result['label'] = 'Disabled';
            $result['error'] = 'This product is disabled.';
            $result['actives'] = 0;
        } else {
            $result['label'] = 'Processing';
            $result['actives'] = 1;
        }
        
        return $result;
    }
    
    public function isRemoved() {
        return $this->status == Product::STATUS_REMOVED ? true : false;
    }
    
    public function isDisabled() {
        return $this->status == Product::STATUS_DISABLED ? true : false;
    }

    /**
     * @param float $price
     * @param float $percentage
     * @return float
     */
    protected function applyDiscount(float $price, float $percentage, string $channel) : float
    {
        $discount = $price * ($percentage / 100);

        if ($discount) {
            $price -= $discount;

            $data = new UpdatedDataType(
                UpdatedDataType::ENTITY_PRICE,
                $this->price,
                $price,
                UpdatedDataType::PRICE_CHANGE_SOURCE_AUTOMATIC
            );

            ChangeLog::log($this->id, $channel, ChangeLog::ACTION_UPDATED, $data);
        }

        return $price;
    }

    /**
     * @param float $price
     * @param float $weight
     * @return float
     */
    protected function applyShippingDeductions(float $price, float $weight, string $channel)
    {
        if (! $weight) {
            return $price;
        }

        $shippingDeduction = ShippingDeduction::whereMerchantId($this->merchant_id)
            ->where('to_weight', '>', $weight)
            ->where('from_weight', '<=', $weight)
            ->first();

        if ($shippingDeduction) {
            $price -= $shippingDeduction->amount;

            $data = new UpdatedDataType(
                UpdatedDataType::ENTITY_PRICE,
                $this->price, // before
                $price,       // after
                UpdatedDataType::PRICE_CHANGE_SOURCE_SHIPPING
            );

            ChangeLog::log($this->id, $channel, ChangeLog::ACTION_UPDATED, $data);
        }

        return $price;
    }

    /**
     * @param float $price
     * @param float $weight
     * @param float $discount
     * @param string $channel
     * @return Product
     */
    public function manipulatePrice(float $price, float $weight, float $discount, string $channel) : Product
    {
        $price = $this->applyDiscount($price, $discount, $channel);
        $price = $this->applyShippingDeductions($price, $weight, $channel);

        $this->price = $price;

        return $this;
    }

    public function removeDeductions() {
        PriceDeductions::where('product_id', $this->id)->delete();
        return $this;
    }
    
    public function addDeduction($reason) 
    {
        PriceDeductions::create([
            'product_id'=> $this->id,
            'reason'    => $reason
        ]);
        
        return $this;
    }
    
    public function priceDeductionHtml() {
        $html = '';
        $lastItem = null;
        foreach($this->priceDeductions() as $item) {
            $html .= $item->reason . '<br>';
            $lastItem = $item;
        }

        if($lastItem) {
            $html .= "Last Update: " . date('m/d/Y h:i A', strtotime($lastItem->updated_at));
        }
        
        return $html;
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public static function create(array $attributes = [])
    {
        $attributes['type'] = $attributes['type'] ?? Product::TYPE_SIMPLE;

        return parent::create($attributes);
    }

    /**
     * Reverse order of firstOrCreate(). Use only when trying to
     * insert a product with a potential identical row in the
     * database that might violate integrity constraints
     *
     * @param array $attributes
     * @return Model
     */
    public static function createOrFirst(array $attributes = [])
    {
        try {
            return parent::create($attributes);
        } catch (QueryException $e) {
            // If it's an integrity constraint violation,
            // return the exsting row.
            if ($e->getCode() == 23000) {
                $query = new static;

                foreach ($attributes as $column => $value) {
                    $query->where($column, $value);
                }

                return $query->first();
            }
            // If it's a different query error, rethrow it.
            else {
                throw $e;
            }
        }
    }

    /**
     * @param Request $request
     * @return $this|static
     */
    public static function createWithChildren(Request $request)
    {
        $attributes = $request->only(
            'name', 'description', 'sku', 'condition', 'manufacturer', 'model_number', 'product_identifier',
            'product_identifier_type', 'msrp', 'price', 'weight', 'weight_unit', 'height', 'width', 'length',
            'dimensions_unit'
        );

        $attributes['merchant_id'] = auth()->id();
        $attributes['channel'] = CHANNEL_MERCHANT_PORTAL;
        $attributes['type'] = count($request->get('variations')) ? Product::TYPE_CONFIGURABLE : Product::TYPE_SIMPLE;
        
        // add status
        if($request->get('weight') > 0) {
            $attributes['status'] = Product::STATUS_ACTIVE;
        } else {
            $attributes['status'] = Product::STATUS_PENDING_NO_WEIGHT;
        }

        if ($parentSku = $request->get('parent_sku')) {
            $parent = Product::where('sku', $parentSku)->first();

            if (! $parent) {
                $request->session()->flash('error', 'Couldn\'t find parent product.');
                return false;
            }
            else if ($parent->parent_id) {
                $request->session()->flash('error', 'The parent product has a parent of it\'s own and cannot have children.');
                return false;
            }

            $parent->increment('variants', 1);
            $attributes['parent_id'] = $parent->id;
        }

        $product = Product::create($attributes);

        // If the product being created has a parent, it cannot have child products.
        if (! $request->get('parent_sku') && $request->get('variations')) {
            static::createVariations($request->get('variations'), $attributes, $product);
        }

        return $product;
    }

    /**
     * When a child is created or updated, we must merge the attributes
     * to the parent variations field.
     *
     * @param Product $parent
     * @param array $attributes
     * @return bool
     */
    public static function updateParentVariations(Product $parent, array $attributes)
    {
        $variations = $parent->variations;

        foreach ($attributes['attributes'] as $name => $value) {
            if (isset($variations[$name])) {
                $variations[$name][] = $value;
            }
            else {
                $variations[$name] = [$value];
            }

            $variations[$name] = array_unique($variations[$name]);
        }

        $parent->variations = $variations;
        $parent->variants = $parent->variants + 1;

        return $parent->save();
    }

    public static function boot()
    {
//        $onSave = function (Product $product) {
//            try {
//                $attributes = \GuzzleHttp\json_decode($product->attributes['attributes'], true);
//            }
//            catch (\Exception $e) {
//                $attributes = [];
//            }
//
//            if ($product->parent && count($attributes)) {
//                $product->parent->mergeChildAttributes($attributes);
//
//                if ($product->parent->type !== static::TYPE_CONFIGURABLE) {
//                    $product->parent->type = static::TYPE_CONFIGURABLE;
//                }
//
//                $product->parent->save();
//            }
//        };
//
//        static::creating($onSave);
//        static::updating($onSave);
    }

    /**
     * @param $variations
     * @param $attributes
     * @param $parent
     */
    protected static function createVariations($variations, $attributes, $parent)
    {
        $parentVariations = [];

        foreach ($variations as $variation) {
            $parent->variants = $parent->variants + 1;

            $childAttributes = array_except($variation, ['sku, quantity', 'type']);
            unset($childAttributes['sku'], $childAttributes['quantity']);

            $attributes['sku'] = $variation['sku'];
            $attributes['parent_sku'] = $parent->sku;
            $attributes['parent_id'] = $parent->id;
            $attributes['quantity'] = $variation['quantity'];
            $attributes['attributes'] = $childAttributes;
            $attributes['type'] = Product::TYPE_SIMPLE;

            foreach ($childAttributes as $name => $value) {
                if (isset($parentVariations[$name]) && ! in_array($value, $parentVariations[$name])) {
                    $parentVariations[$name][] = $value;
                }
                else {
                    $parentVariations[$name] = [$value];
                }
            }

            $parent->variations = $parentVariations;

            Product::create($attributes);
        }

        $parent->save();
    }

    /**
     * @param int $merchantId
     * @param bool $asArray
     * @return array
     */
    public static function getValidationRules(int $merchantId, bool $asArray = false)
    {
        $rules = [
            'name'                    => 'required|string|max:255',
            'sku'                     => "required|string|unique:products,sku,NULL,sku,merchant_id,$merchantId|max:255",
            'categories.*'            => 'numeric',
            'condition'               => 'required|in:new,used,reconditioned',
            'manufacturer'            => 'string',
            'model_number'            => 'max:255',
            'product_identifier'      => 'required_with:product_identifier_type|max:255',
            'product_identifier_type' => 'required_with:product_identifier',
//            'msrp'                    => 'numeric',
            'price'                   => 'required',
            'weight'                  => 'numeric|required_with:weight_unit',
            'weight_unit'             => 'required_with:weight|in:lbs,kg',
            'height'                  => 'numeric|required_with:width,length,dimensions_unit',
            'width'                   => 'numeric|required_with:height,length,dimensions_unit',
            'length'                  => 'numeric|required_with:width,height,dimensions_unit',
            'dimensions_unit'         => 'required_with:height,width,length|in:inches,centimeters',
            'parent_sku'              => 'string|max:255',
            'variations.*.sku'        => 'required|string|max:255',
            'variations.*.quantity'   => 'numeric',
        ];

        if ($asArray) {
            foreach ($rules as $key => $value) {
                $rules['products.*.'.$key] = $value;
                unset($rules[$key]);
            }
        }

        return $rules;
    }
    
    public function content() 
    {
        return $this->hasOne('App\Models\Products\ProductDescription', 'product_id');
    }
    public function getContentDescriptionAttribute()
    {
        if($this->content) {
            return $this->content->description;
        }

        return '';
    } 
}
