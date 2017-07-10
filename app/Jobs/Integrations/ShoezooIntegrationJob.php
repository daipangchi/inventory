<?php

namespace App\Jobs\Integrations;

use App\Channels\Shoezoo\Adapter;
use App\Channels\Shoezoo\CatalogApi;
use App\Jobs\Job;
use App\Models\Merchants\Merchant;
use App\Models\Products\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class ShoezooIntegrationJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    /**
     * @var string
     */
    public $connection = 'integrations';

    /**
     * @var string
     */
    public $queue = 'integrations';

    /**
     * @var Collection|Product[]
     */
    protected $productsCache;

    const SHOEZOO_MERCHANT_ID = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setup();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');

        $merchant = Merchant::find(static::SHOEZOO_MERCHANT_ID);
        $adapter = new Adapter($merchant);
        $products = $this->getProducts();
        $start = microtime(true);

        //$products->where('name');

        foreach ($products as $product) {
            $attributes = $adapter->extractAttributes($product);
            $categoryIds = $adapter->extractCategoryIds([$product['category.name'], $product['shoe_type']]);
            $imageUrls = $adapter->extractImageUrls($product['parentImages']);

            if ($this->productExists($attributes['sku'])) {
                $saved = Product::whereMerchantId($merchant->id)->whereSku($attributes['sku'])->first();
                $saved->update($attributes);
            }
            else {
                /* @var $saved Product */
                $saved = Product::create($attributes);
                $saved->downloadAndAttachImages($imageUrls);
                $saved->attachCategories($categoryIds);
                $saved->status = Product::STATUS_ACTIVE;
            }
        }

        // Attach parent-child relations
        Product::whereType('configurable')->each(function (Product $parent) {
            // bulk update
            Product::where('parent_sku', $parent->sku)->update([
                'parent_id'  => $parent->id,
                'parent_sku' => $parent->sku,
            ]);

            // update parent variations
            Product::where('parent_sku', $parent->sku)->each(function (Product $child) use ($parent) {
                $parent->mergeChildAttributes($child->attributes ?: []);
            });

            $parent->save();
        });

        echo ((microtime(true) - $start) / 60).PHP_EOL;
    }

    public function setup()
    {
        $this->productsCache = Product::whereMerchantId(static::SHOEZOO_MERCHANT_ID)->get(['id', 'sku']);
    }

    /**
     * @param $sku
     * @return bool
     */
    protected function productExists($sku)
    {
        return (bool)$this->productsCache->where('sku', $sku)->first();
    }

    /**
     * @param $products
     * @param $columns
     * @return array
     */
    protected function transformToAssociative($products, $columns)
    {
        foreach ($products as $index => $product) {
            $new = [];

            foreach ($product as $i => $cell) {
                $new[$columns[$i]] = $cell;
            }

            $products[$index] = $new;
        }

        unset($product);

        $products = array_filter($products, function ($product) {
            $isEmpty = true;

            foreach ($product as $value) {
                if ($value !== '') {
                    $isEmpty = false;
                    continue;
                }
            }

            if ($isEmpty) {
                return false;
            }
            else {
                return true;
            }
        });

        return $products;
    }

    /**
     * @return mixed
     */
    protected function getShoeZooInventory()
    {
        $dropShippingInventory = new CatalogApi();
        $dropShippingInventory->getCatalogList();

        $count = 0;

        do {
            if ($count++ > 30) {
                $this->error('Failed');
                die;
            }

            $response = $dropShippingInventory->getCatalogList(true);
            $success = isset($response->success) && $response->success;

            sleep(60);
        } while (! $success);

        return $response;
    }

    /**
     * @param $products
     * @return array
     */
    protected function sortProductsByType($products)
    {
        // order by product type (configurable first)
        usort($products, function ($a, $b) {
            if ($a[2] == 'configurable' && $b[2] == 'simple') {
                return -1;
            }

            if ($a[2] == 'simple' && $b[2] == 'configurable') {
                return 1;
            }

            return 0;
        });

        return array_filter($products);
    }

    /**
     * @return array
     */
    protected function getProducts()
    {
        if (app()->environment('local')) {
            $products = array_map('str_getcsv', file(base_path('database/seeds/data/shoezoo_inventory.csv')));
        }
        else {
            $response = $this->getShoeZooInventory();
            $products = array_map('str_getcsv', file($response->file));
        }

        $columns = array_splice($products, 0, 1)[0];
        $products = $this->sortProductsByType($products);
        $products = $this->transformToAssociative($products, $columns);

        return $products;
    }
}
