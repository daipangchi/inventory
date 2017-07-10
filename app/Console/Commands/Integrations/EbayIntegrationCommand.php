<?php

namespace App\Console\Commands\Integrations;

use App\Jobs\Integrations\EbayIntegrationJob;
use App\Models\Merchants\Merchant;
use Illuminate\Console\Command;
use Swap;

class EbayIntegrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:ebay {merchant_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var
     */
    protected $service;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $merchant = Merchant::find($this->argument('merchant_id'));
        $job = new EbayIntegrationJob($merchant, 'Artisan');

        if (app()->environment('local')) {
            $job->handle();
        } else {
            dispatch($job);
        }
    }
}
