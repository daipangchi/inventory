<?php

namespace App\Console\Commands;

use App\Channels\Shoezoo\OrderApi;
use App\Models\OrderProduct;
use Illuminate\Console\Command;

class ExportShoezooOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
        $api = new OrderApi();

        $data = [
            'productList' => []
        ];

        $orders = OrderProduct::whereStatus(OrderProduct::STATUS_PENDING)->get()
            ->each(function (OrderProduct $order) use (&$data) {
                $data['productList'][] = [
                    'sku'   => $order->sku,
                    'price' => $order->price,
                    'qty'   => $order->qty,
                ];
            });

        if (empty($data['productList'])) {
            return;
        }

        $data['orderNumber'] = $orders->first()->order_number;

        $response = $api->createOrder($data);

        if (isset($response->success) && $response->success) {
            $api->finalizeOrder($response->orderIncrementId);
        }
        else {
            $exceptionArray = json_decode($response->getMessage(), true);

            \Log::info($exceptionArray);
        }
    }
}
