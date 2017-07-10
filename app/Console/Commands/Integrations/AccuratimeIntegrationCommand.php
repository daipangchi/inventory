<?php

namespace App\Console\Commands\Integrations;

use App\Jobs\Integrations\AccuratimeIntegrationJob;
use App\Models\Merchants\Merchant;
use Illuminate\Console\Command;

class AccuratimeIntegrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:accura';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $merchant = Merchant::whereEmail('adamkrief@gmail.com')->first();
        $job = new AccuratimeIntegrationJob($merchant ?? Merchant::first(), 'Artisan');

        if (app()->environment('local')) {
            $job->handle();
        } else {
            dispatch($job);
        }
    }
}
