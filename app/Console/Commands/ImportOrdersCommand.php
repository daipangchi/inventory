<?php

namespace App\Console\Commands;

use App\CustomerPortal\Orders\ApiOrders;
use Illuminate\Console\Command;

/**
 * Class ImportOrdersCommand
 * @package App\Console\Commands
 */
class ImportOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:import {--page_num=} {--page_size=} {--from_number=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import orders from Magento store';

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
     * Imports orders from Magento store.
     *
     * @return void
     */
    public function handle()
    {
        $pageNum = $this->option('page_num') ?: 1;
        $pageSize = $this->option('page_size') ?: 100;
        $fromNumber = $this->option('from_number') ?: 1;

        $orders = new ApiOrders();

        $response = $orders->importOrders([
            'pageNum'    => $pageNum,
            'pageSize'   => $pageSize,
            'fromNumber' => $fromNumber,
        ]);

        $json_data = json_decode($response);

        if ($json_data->status == 'error') {
            $this->error($json_data->message);
        }
        else if ($json_data->status == 'fail') {
            foreach ($json_data->data as $error) {
                $this->error($error);
            }
        }
        else {
            $this->info('Orders have been imported successfully');
        }
    }
}
