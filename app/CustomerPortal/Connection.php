<?php

namespace App\CustomerPortal;

use App\Models\Merchants\Merchant;
use App\Models\Order;
use App\Models\OrderProduct;
use Config;
use GuzzleHttp\Client;

class Connection
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $scheme = app()->environment('production') ? 'https://' : 'http://';
        $domain = Config::get('app.customer_portal_url');

        $this->client = new Client([
            'base_uri' => $scheme.$domain,
        ]);
    }

    /**
     * @param Merchant $merchant
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function createMerchant(Merchant $merchant)
    {
        return $this->client->request('get', "/order-command/ordercommand/merchant", [
            'query' => [
                'command'       => 'create',
                'merchant_id'   => $merchant->id,
                'merchant_name' => $merchant->name,
            ],
        ]);
    }

    /**
     * @param string $orderNumber
     * @param int $merchantId
     * @param string $command
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function createOrder(string $orderNumber, int $merchantId, string $command)
    {
        $uri = "/order-command/ordercommand/order_command".
            "/order_number/$orderNumber".
            "/merchant_id/$merchantId".
            "/command/$command";

        $order = Order::whereOrderNumber($orderNumber)->first();
        $items = OrderProduct::whereMerchantId($merchantId)
            ->whereOrderId($order->id)
            ->select(['item_id', 'qty'])
            ->get()->toArray();

        $items = array_map(function ($item) {
            // todo get real tracking number
            $item['tracking_no'] = str_random(8);

            return $item;
        }, $items);

        return $this->client->request('post', $uri, ['json' => $items]);
    }

    /**
     * @param $page
     * @param $pageSize
     * @param $from
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function importOrder($page, $pageSize, $from)
    {
        $response = $this->client->get("/export/orders/index/pageNum/$page/pageSize/$pageSize/from_number/$from");

        $data = json_decode($response->getBody()->getContents());

        if ($data && $data->status == 'success') {
            foreach ($data->data->orders as $order) {
                $orderModel = Order::firstOrCreate(['order_number' => $order->id]);

                $orderModel->update([
                    'customer_name'      => $order->name,
                    'customer_email'     => $order->email,
                    'customer_telephone' => $order->telephone,
                    'country'            => $order->country,
                    'city'               => $order->city,
                    'street'             => implode(',', $order->street),
                    'pincode'            => $order->pincode,
                    'weight'             => $order->weight,
                    'grand_total'        => $order->revenue,
                ]);

                foreach ($order->items as $item) {
                    $order = OrderProduct::whereMerchantId($item->merchant_id ?: 1)
                        ->whereOrderId($orderModel->id)
                        ->whereSku($item->sku)
                        ->whereItemId($item->item_id)
                        ->first();

                    if ($order) {
                        $order->update([
                            'name'  => $item->name,
                            'price' => $item->price,
                            'qty'   => $item->qty,
                        ]);
                    }
                    else {
                        OrderProduct::create([
                            'merchant_id' => $item->merchant_id ?: 1,
                            'order_id'    => $orderModel->id,
                            'sku'         => $item->sku,
                            'item_id'     => $item->item_id,
                            'name'        => $item->name,
                            'price'       => $item->price,
                            'qty'         => $item->qty,
                            'status'      => OrderProduct::STATUS_PENDING,
                        ]);
                    }
                }
            }
        }

        return $response;
    }
}
