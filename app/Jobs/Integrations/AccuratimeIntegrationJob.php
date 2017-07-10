<?php

namespace App\Jobs\Integrations;

use App\Channels\Accuratime\Adapter;
use App\Jobs\Job;
use App\Models\Merchants\Merchant;
use App\Models\Products\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AccuratimeIntegrationJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var Merchant
     */
    protected $merchant;

    /**
     * @var Adapter
     */
    protected $adapter;

    /**
     * Create a new job instance.
     *
     * @param Merchant $merchant
     */
    public function __construct(Merchant $merchant)
    {
        $this->merchant = $merchant;
        $this->adapter = new Adapter($merchant);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $groups = $this->combineCollections($this->getWatches());
        $existingProducts = $this->merchant->products;

        foreach ($groups as $watches) {
            foreach ($watches as $watch) {
                $attributes = $this->adapter->extractAttributes($watch);
                $categoryIds = $this->adapter->extractCategoryIds($watch);
                $imageUrls = $this->adapter->extractImageUrls($watch);

                /* @var $saved Product */
                if ($saved = $existingProducts->where('sku', $attributes['sku'])->first()) {
                    $saved->update($attributes);
                }
                else {
                    $saved = Product::create($attributes);
                    $saved->attachCategories($categoryIds);
                    $saved->downloadAndAttachImages($imageUrls);
                    $saved->status = Product::STATUS_ACTIVE;

                    $existingProducts->add($saved);
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function getWatches()
    {
        $csv = parse_csv(base_path('database/seeds/data/mcs.csv'));
        $csv = $this->removeDuplicates($csv);

        $watches = csv_to_keyed_array($csv);
        $duplicateSkus = array_unique(array_duplicates(array_column($watches, 'Model')));

        foreach ($duplicateSkus as $sku) {
            $i = 0;
            foreach ($watches as $index => $watch) {
                if ($watch['Model'] == $sku) {
                    $watches[$index]['Model'] = "$sku-$i";
                    $i++;
                }
            }
        }

        return $watches;
    }

    /**
     * Group watches by
     *
     * @param $watches
     * @return array
     */
    protected function combineCollections($watches)
    {
        $collections = [];

        foreach ($watches as $watch) {
            $name = $watch['Collection'];

            if (isset($collections[$name])) {
                $collections[$name][] = $watch;
            }
            else {
                $collections[$name] = [$watch];
            }
        }

        return $collections;
    }

    /**
     * @param $name
     * @return string
     */
    protected function generateParentSku($name)
    {
        $sku = substr(md5($name), 0, 10);
        $index = 0;

        $existing = Product::whereMerchantId($this->merchant->id)->whereSku($sku)->get();

        while ($existing->where('sku', $sku)->first()) {
            $newSku = $sku.$index;
        }

        return $newSku ?? $sku;
    }

    /**
     * @param $csv
     * @return array
     */
    protected function removeDuplicates($csv)
    {
        $csv = array_map(function ($row) {
            return implode('|||', array_trim_values($row));
        }, $csv);

        $csv = array_unique($csv);

        return array_map(function ($row) {
            return explode('|||', $row);
        }, $csv);
    }
}
