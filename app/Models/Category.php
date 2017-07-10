<?php

namespace App\Models;

use App\ModelCacher;
use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

/**
 * App\Models\Category
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $parent_id
 * @property string $name
 * @property string $name_hebrew
 * @property string $description
 * @property string $image
 * @property float $fee
 * @property string $path_by_category_id
 * @property string $path_by_name
 * @property string $path_by_name_hebrew
 * @property boolean $level
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $subcategories
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Category[] $children
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Products\Product[] $products
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CategoryTax[] $taxes
 * @property-read \App\Models\CategoryDeduction $deduction
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AmazonCategory[] $amazonCategories
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EbayCategory[] $ebayCategories
 * @property-read \App\Models\Category $parent
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Category whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Category whereCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Category whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Category whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Category whereNameHebrew($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Category whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Category whereImage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Category whereFee($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Category wherePathByCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Category wherePathByName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Category wherePathByNameHebrew($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Category whereLevel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Category whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Category extends Model
{
    use ModelCacher;

    /**
     * @var string
     */
    protected $primaryKey = 'category_id';

    /**
     * @var string
     */
    protected $table = 'categories';

    /**
     * @var array
     */
    protected $fillable = [
        'category_id',
        'parent_id',
        'channel',
        'name',
        'name_hebrew',
        'level',
        'description',
        'fee',
        'custom_code'
    ];

    /**
     * This will be used to check what the deepest category level is.
     * Save as static so we only need to query it once per request.
     *
     * @var int
     */
    protected static $maxCategoryLevel;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('children');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'categories_products', 'category_id', 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taxes()
    {
        return $this->hasMany(CategoryTax::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deduction()
    {
        return $this->hasOne(CategoryDeduction::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function amazonCategories()
    {
        return $this->hasMany(AmazonCategory::class, 'cadabra_category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ebayCategories()
    {
        return $this->hasMany(EbayCategory::class, 'cadabra_category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->hasOne(static::class, 'category_id', 'parent_id');
    }

    /**
     * @return mixed
     */
    public function generateHebrewPath()
    {
        $withParents = [$this->name_hebrew];
        $parents = $this->buildParents($this, $withParents);

        return implode('/', $parents);
    }

    /**
     * @param $category
     * @param $withParents
     * @return mixed
     */
    private function buildParents($category, &$withParents)
    {
        if ($parent = $category->parent()->first()) {
            array_unshift($withParents, $parent->name_hebrew);
            $this->buildParents($parent, $withParents);
        }

        return $withParents;
    }

    /**
     * To be used for for the categories page.
     *
     * @param Collection $categories
     * @return string
     */
    public function toHtml(Collection $categories = null)
    {
        $selected = $categories && $categories->where('category_id', $this->category_id)->first() ? 'true' : 'false';

        $html = "<li class=\"category\" data-category-id=\"$this->category_id\" data-category-name=\"$this->name\" data-category-level=\"$this->level\" data-selected=\"$selected\">";
        $html .= $this->name;
        $html .= "<ul>{$this->childrenToHtml($categories)}</ul>";
        $html .= '</li>';

        return $html;
    }

    /**
     * To be used for for the categories page.
     *
     * @param Collection $categories
     * @return string
     */
    private function childrenToHtml(Collection $categories = null)
    {
        $html = '';

        foreach ($this->children as $child) {
            $selected = $categories && $categories->where('category_id', $child->category_id)->first() ? 'true' : 'false';

            $html .= "<li class=\"category\" data-category-id=\"$child->category_id\" data-category-name=\"$child->name\" data-category-level=\"$child->level\" data-selected=\"$selected\">";
            $html .= $child->name;
            $html .= "<ul>{$child->childrenToHtml($categories)}</ul>";
            $html .= '</li>';
        }

        return $html;
    }

    /**
     * @param null $level
     * @return mixed
     */
    public static function level($level = null)
    {
        $query = static::query();

        if (! is_null($level)) {
            $query->whereLevel($level);
        }

        return $query;
    }

    /**
     * @return mixed
     */
    public static function getAllNested()
    {
        return Category::level(1)->with('children', 'taxes')->select('name', 'level', 'id', 'category_id')->get();
    }

    /**
     * Get the deepest category level. Save as class static
     * so we don't have to query it every time.
     *
     * @return int
     */
    public static function getMaxCategoryLevel()
    {
        if (static::$maxCategoryLevel) {
            return static::$maxCategoryLevel;
        }

        return static::$maxCategoryLevel = (int)static::max('level');
    }

    /**
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function (Category $category) {
            // Set category_id if not set already.
            if (! $category->category_id) {
                $category->category_id = Category::max('category_id') + 1;
            }

            // Set path_by_category_id if not set already.
            if (! $category->path_by_category_id) {
                if ($category->parent_id) {
                    $category->path_by_category_id = $category->parent->path_by_category_id.','.$category->category_id;
                }
                else {
                    $category->path_by_category_id = $category->category_id;
                }
            }

            // Set path_by_name if not set already.
            if (! $category->path_by_name) {
                if ($category->parent_id) {
                    $category->path_by_name = $category->parent->path_by_name.' > '.$category->name;
                }
                else {
                    $category->path_by_name = $category->name;
                }
            }

            // Set path_by_name_hebrew if not set already.
            if (! $category->path_by_name_hebrew) {
                $name = $category->name_hebrew ?: $category->name;

                if ($category->parent_id) {
                    $category->path_by_name_hebrew = $category->parent->path_by_name_hebrew.' > '.$name;
                }
                else {
                    $category->path_by_name_hebrew = $name;
                }
            }
        });

        static::saving(function (Category $category) {
            $parent = Category::find($category->parent_id);

            if ($parent) {
                $category->level = $parent->level + 1;
            }
        });

        static::deleting(function (Category $category) {
            $category->load('children');

            foreach ($category->children as $child) {
                $child->delete();
            }
        });
    }
}
