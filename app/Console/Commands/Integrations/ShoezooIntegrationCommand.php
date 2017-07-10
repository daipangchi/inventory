<?php

namespace App\Console\Commands\Integrations;

use App\Jobs\Integrations\ShoezooIntegrationJob;
use Illuminate\Console\Command;

class ShoezooIntegrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:shoezoo';

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
        $job = new ShoezooIntegrationJob();

        if (app()->environment('local')) {
            $job->handle();
        } else {
            dispatch($job);
        }
    }
}
