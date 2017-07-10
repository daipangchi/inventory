<?php

namespace App\Jobs;

use App\Models\Merchants\Merchant;
use App\Models\SyncJobLog;
use Artisan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MagmiImportJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var string
     */
    public $queue = 'magmi';

    /**
     * @var Merchant
     */
    protected $merchant;

    /**
     * @var SyncJobLog
     */
    protected $syncJobLog;

    /**
     * Create a new job instance.
     *
     * @param Merchant $merchant
     * @param SyncJobLog $syncJobLog
     */
    public function __construct(Merchant $merchant, SyncJobLog $syncJobLog = null)
    {
        $this->merchant = $merchant;
        $this->syncJobLog = $syncJobLog;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if ($this->syncJobLog) {
            if ($this->syncJobLog->products_created > 0) {
                Artisan::call('magmi:create', ['merchant_id' => $this->merchant->id]);
//                Artisan::call('magmi:import', ['merchant_id' => $this->merchant->id, 'action' => 'xcreate']);
            }

            if ($this->syncJobLog->products_updated > 0) {
                Artisan::call('magmi:update', ['merchant_id' => $this->merchant->id]);
//                Artisan::call('magmi:import', ['merchant_id' => $this->merchant->id, 'action' => 'update']);
            }
        }
        else {
            Artisan::call('magmi:create', ['merchant_id' => $this->merchant->id]);
//            Artisan::call('magmi:import', ['merchant_id' => $this->merchant->id, 'action' => 'xcreate']);

            Artisan::call('magmi:update', ['merchant_id' => $this->merchant->id]);
//            Artisan::call('magmi:import', ['merchant_id' => $this->merchant->id, 'action' => 'update']);
        }
    }
}
