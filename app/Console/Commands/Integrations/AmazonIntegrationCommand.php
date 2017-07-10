<?php

namespace App\Console\Commands\Integrations;

use App\Jobs\Integrations\AmazonIntegrationJob;
use App\Models\Merchants\Merchant;
use Illuminate\Console\Command;

class AmazonIntegrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:amazon {merchant_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $merchant = Merchant::find($this->argument('merchant_id'));
        $job = new AmazonIntegrationJob($merchant, 'Artisan');
         
        if (app()->environment('local')) {
            $job->handle();
        } else {
            dispatch($job);
        }
    }
}
