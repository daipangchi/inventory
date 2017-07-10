<?php

namespace App\Console\Commands;

use App\CustomerPortal\Orders\ApiCommands;
use Illuminate\Console\Command;

/**
 * Class SendMerchantOrderCommand
 * @package App\Console\Commands
 */
class SendMerchantOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:push {order_number} {merchant_id} {order_command}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send merchant order command to Magento';

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
     * Sends merchant order command to Magento store.
     *
     * @return void
     */
    public function handle()
    {
        $send_command = new ApiCommands();

        $response = $send_command->createCommand([
            'order_number' => $this->argument('order_number'),
            'merchant_id'  => $this->argument('merchant_id'),
            'command'      => $this->argument('order_command'),
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
            $this->info('Command has been sent successfully');
        }
    }
}
