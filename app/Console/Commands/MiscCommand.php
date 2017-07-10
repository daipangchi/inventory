<?php

namespace App\Console\Commands;

use App\Channels\Shoezoo\CatalogApi;
use App\Jobs\Integrations\AccuratimeIntegrationJob;
use App\Models\Merchants\Merchant;
use App\Models\Products\Product;
use App\Models\Schedule;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class MiscCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * To be used for quick testing of miscellaneous code from the command-line.
     *
     * @return mixed
     */
    public function handle()
    {
        Product::whereParentId(null)->select('id', 'parent_sku')->get()->each(function (Product $p) {
            $p->update(['parent_sku' => str_replace(' ', '_', $p->parent_sku)]);
        });
    }

    protected function shoezoo()
    {
        $api = new CatalogApi();

        $response = $api->getCatalogList();

        if (isset($response->success) && $response->success) {
            echo "<pre>".print_r($response, 1)."</pre>";
        }
        else {
            echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
        }

        do {
            $response = $api->getCatalogList(true);

            $success = isset($response->success) && $response->success;

            if ($success) {
                echo "<pre>".print_r($response, 1)."</pre>";
            }
            else {
                echo "<pre>".print_r($response->getMessage(), 1)."</pre>";
            }
            sleep(30);
        } while (! $success);
        echo PHP_EOL;
    }

    protected function seedSchedules()
    {
        $progress = new ProgressBar(new ConsoleOutput(), 24 * 60 * 60);
        $progress->start();

        for ($h = 0; $h < 24; $h++) {
            for ($m = 0; $m < 60; $m++) {
                for ($s = 0; $s < 60; $s++) {
                    $h = str_pad($h, 2, '0', STR_PAD_LEFT);
                    $m = str_pad($m, 2, '0', STR_PAD_LEFT);
                    $s = str_pad($s, 2, '0', STR_PAD_LEFT);

                    $schedule = new Schedule();
                    $schedule->merchant_id = 1;
                    $schedule->channel = CHANNEL_EBAY;
                    $schedule->run_at = "$h:$m:$s";
                    $schedule->save();

                    $progress->advance();
                }
            }
        }

        $progress->finish();
    }

    protected function seedAccuratime()
    {
        $csv = parse_csv(base_path('database/seeds/data/mcs.csv'));

        // Remove last 3 columns which have no content.
        $csv = array_map(function ($row) {
            return array_splice($row, 0, -3);
        }, $csv);

        $products = csv_to_keyed_array($csv);

        $watchesMen = [153, 171, 173];
        $watchesWomen = [153, 171, 172];

        dd((array_filter($products, function ($row) {
            return $row['Name'] == "Armani Exchange Men's Chronograph";
        })));

        foreach ($products as $product) {
            $attributes = [
                'sku'                     => $product['Model'],
                'name'                    => $product['Name'],
                'description'             => $product['Description'],
                'quantity'                => $product['Quantity'],
                'product_identifier'      => $product['UPC'],
                'product_identifier_type' => 'upc',
                'brand'                   => $product['Brand'],
                'attributes'              => [
                    'Dial'     => $product['Dial'],
                    'Movement' => $product['Movement'],
                ],
                'specs'                   => [
                    'Collection' => $product['Collection'],
                ],
            ];

            $imageUrl = $product['Image Url'];
        }

        dd($products[0]);
    }
}
