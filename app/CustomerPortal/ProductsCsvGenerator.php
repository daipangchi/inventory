<?php


namespace App\CustomerPortal;


use App\Magmi\CsvProduct\CsvProduct;
use App\Models\Products\ChangeLog;
use App\Models\Products\Product;
use Illuminate\Support\Collection;

class ProductsCsvGenerator
{
    public $tempFile;

    /**
     * GenerateCsv constructor.
     *
     * @internal param $tempFile
     *
     */
    public function __construct()
    {
        $this->tempFile = \Config::get('magmi.path_to_products_csv');
    }

    public function generate()
    {
        $productsArray = [$this->getColumns()];

        $dbProducts = $this->getProductsToSync();

        /** @var Product $product */
        foreach ($dbProducts as $product) {
            $csvProduct = new CsvProduct($product);

            if ($csvProduct->isTypeUnknown()) {
                continue;
            }

            $productsArray[] = array_values([
                'sku'                     => $csvProduct->id,
                'attribute_set'           => 'Default',
                'type'                    => $csvProduct->getProductType(),
                'store'                   => '',
                'configurable_attributes' => $csvProduct->isConfigurable() ? 'condition' : null,
                'price'                   => $csvProduct->price,
                'qty'                     => $csvProduct->quantity,
                'is_in_stock'             => 1,
                'manage_stock'            => 1,
                'use_config_manage_stock' => '',
                'status'                  => $csvProduct->status,
                'visibility'              => 'Catalog, Search',
                'weight'                  => $csvProduct->weight,
                'categories'              => '',
                'tax_class_id'            => 'None',
                'thumbnail'               => $csvProduct->getMainImage(),
                'small_image'             => $csvProduct->getMainImage(),
                'image'                   => $csvProduct->getMainImage(),
                'media_gallery'           => $csvProduct->getImagesMagmiField(),
                'name'                    => $csvProduct->name,
                'merchant_id'             => $csvProduct->merchant_id,
                'condition'               => $csvProduct->condition,


//                'description'             => 'description',
//                'short_description'       => 'description',
//                'msrp'                    => $csvProduct->msrp,
//                'bundle_options'          => $csvProduct->isBundle() ? '-*;Color:Color' : null,
//                'bundle_skus'             => $csvProduct->isBundle() ? $csvProduct->getChildrenSkus() : null,
//                'simples_skus'            => $csvProduct->isConfigurable() ? $csvProduct->getChildrenSkus() : null,
            ]);

            if ($logEntry = ChangeLog::where('product_id', $csvProduct->id)->first()) {
                $logEntry->exported_to_customer_portal = true;
                $logEntry->save();
            }
        }

        $this->writeFile($productsArray);
    }

    public function getColumns()
    {
        // sku	attribute_set	type	store	configurable_attributes	color	size	Material	Warranty	price	qty	is_in_stock	manage_stock	use_config_manage_stock	status	visibility	weight	categories	tax_class_id	thumbnail	small_image	image	media_gallery	name	merchant_id	condition
        return [
            'sku',
            'attribute_set',
            'type',
            'store',
            'configurable_attributes',

//            'color',
//            'size',
//            'material',

            'price',
            'qty',
            'is_in_stock',
            'manage_stock',
            'use_config_manage_stock',
            'status',
            'visibility',
            'weight',
            'categories',
            'tax_class_id',
            'thumbnail',
            'small_image',
            'image',
            'media_gallery',
            'name',
            'merchant_id',
            'condition',


//            'description',
//            'short_description',
//            'msrp',
//            'bundle_options',
//            'bundle_skus',
//            'simples_skus',
        ];
    }

    /**
     * @return Collection
     */
    public function getProductsToSync()
    {
        $products = Product
            ::with('categories', 'images', 'changeLogs')// ->where('is_published', true)
            ->whereHas('changeLogs', function ($q) {
                $q->where('exported_to_customer_portal', 0);
            })
            ->orderBy('type', 'desc')
            ->get();

        return $products;
    }

    /**
     * @param $productsArray
     */
    private function writeFile($productsArray)
    {
        $fp = fopen($this->writePath, 'w');

        foreach ($productsArray as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);
    }
}
