<?php

namespace App\Jobs;

use App\Models\Products\ChangeLog;
use App\Models\Products\Product;

/**
 * @property Product product
 * @property string action
 * @property string channel
 */
class LogProductChanges extends Job
{
    protected $product;
    protected $action;
    protected $channel;

    /**
     * Create a new job instance.
     *
     * @param Product $product
     * @param $action
     * @param $channel
     */
    public function __construct(Product $product, $action, $channel)
    {
        $this->product = $product;
        $this->action = $action;
        $this->channel = $channel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ChangeLog::create([
            'product_id' => $this->product->id,
            'action'     => $this->action,
            'channel'    => $this->channel,
        ]);
    }
}
