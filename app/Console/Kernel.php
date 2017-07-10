<?php

namespace App\Console;

use App\Jobs\Integrations\AmazonIntegrationJob;
use App\Jobs\Integrations\EbayIntegrationJob;
use App\Jobs\MagmiImportJob;
use App\Models\Merchants\Merchant;
use Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Integrations\AmazonIntegrationCommand::class,
        Commands\Integrations\ShoezooIntegrationCommand::class,
        Commands\Integrations\EbayIntegrationCommand::class,
        Commands\Integrations\AccuratimeIntegrationCommand::class,
        Commands\MiscCommand::class,

        Commands\Magmi\MagmiCreateCommand::class,
        Commands\Magmi\MagmiUpdateCommand::class,
        Commands\Magmi\MagmiImportCommand::class,

        Commands\SendMerchantOrderCommand::class,
        Commands\ImportOrdersCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $scheduler
     * @return void
     */
    protected function schedule(Schedule $scheduler)
    {
        try {

            $scheduler->call(function () {
                $this->runEbaySyncs();
                $this->runAmazonSyncs();
            })->dailyAt('00:00:00');

            $scheduler->call(function () {
                Artisan::call('order:import');
            })->everyMinute();

            $scheduler->call(function () {
                $this->runCustomSyncs();
            })->hourly();

            // $scheduler->call(function () {
            //     Merchant::all()->each(function (Merchant $merchant) {
            //         dispatch(new MagmiImportJob($merchant));
            //     });
            // })->hourly();

        } catch (\Exception $e) {
            \Log::error($e->getMessage(), ['from' => 'scheduler']);
        }
    }

    /**
     * @return void
     */
    public function runAmazonSyncs()
    {
        $merchants = Merchant::where('amazon_seller_id', '!=', '')->get();

        foreach ($merchants as $merchant) {
            dispatch(new AmazonIntegrationJob($merchant, 'Automatic Scheduler'));
        }
    }

    /**
     * @return void
     */
    public function runEbaySyncs()
    {
        $merchants = Merchant::where('ebay_auth_token', '!=', '')
            ->where('ebay_auth_token_expiration', '!=', '0000-00-00 00:00:00')
            ->whereRaw('ebay_auth_token_expiration > NOW()')
            ->get();

        foreach ($merchants as $merchant) {
            dispatch(new EbayIntegrationJob($merchant, 'Automatic Scheduler'));
        }
    }

    /**
     * @param Schedule $scheduler
     */
    protected function runCustomSyncs()
    {
        
        $amazonSchedules = \App\Models\Schedule::distinct()
                                                ->select('merchant_id')
                                                ->where("channel", CHANNEL_AMAZON)
                                                ->where("run_at", date('H:00:00'))
                                                ->get();

        $ebaySchedules   = \App\Models\Schedule::distinct()
                                                ->select('merchant_id')
                                                ->where("channel", CHANNEL_EBAY)
                                                ->where("run_at", date('H:00:00'))
                                                ->get();

        foreach ($ebaySchedules as $s) {
            $merchant = Merchant::find($s->merchant_id);

            if ($merchant) {
                dispatch(new EbayIntegrationJob($merchant, 'Custom Scheduler'));
            }
        }

        foreach ($amazonSchedules as $s) {
            $merchant = Merchant::find($s->merchant_id);

            if ($merchant) {
                dispatch(new AmazonIntegrationJob($merchant, 'Custom Scheduler'));
            }
        }

    }
}
