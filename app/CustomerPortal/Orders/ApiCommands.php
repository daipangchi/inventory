<?php

namespace App\CustomerPortal\Orders;

use App\Models\Order;
use App\Models\OrderProduct;
use GuzzleHttp\Client;

/**
 * Class ApiCommands
 * @package App\CustomerPortal\Orders
 */
class ApiCommands
{
    /**
     * @param array $command
     * @return string
     */
    public function createCommand($command)
    {
        $order_number = $command['order_number'];
        $merchant_id = $command['merchant_id'];
        $command_par = $command['command'];

        $scheme = app()->environment('local', 'dev') ? 'http://' : 'https://';
        $url = \Config::get('app.customer_portal_url');

        $uri = "/order-command/ordercommand/order_command".
            "/order_number/$order_number".
            "/merchant_id/$merchant_id".
            "/command/$command_par";

        $client = new Client([
            'base_uri' => $scheme.$url,
            'timeout'  => 1200,
        ]);

        $order = Order::whereOrderNumber($order_number)->first();
        $items = OrderProduct::whereMerchantId($merchant_id)
            ->whereOrderId($order->id)
            ->select(['item_id', 'qty'])
            ->get()->toArray();

        $items = array_map(function ($item) {
            // todo get real tracking number
            $item['tracking_no'] = str_random(8);

            return $item;
        }, $items);

        $response = $client->request('post', $uri, [
            'json' => $items
        ]);

        return $response->getBody()->getContents();
    }
}
