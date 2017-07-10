<?php

namespace App\CustomerPortal\Orders;

use App\Models\Order;
use App\Models\OrderProduct;
use GuzzleHttp\Client;

/**
 * Class ApiOrders
 * @package App\CustomerPortal\Orders
 */
class ApiOrders
{
    /**
     * @param Request array $params
     * @return string
     */
    public function importOrders($params = null)
    {
        $scheme = app()->environment('local', 'dev') ? 'http://' : 'https://';

        $client = new Client([
            'base_uri' => $scheme.\Config::get('app.customer_portal_url'),
            'timeout'  => 500,
        ]);

        $response = $client->get(
            '/export/orders/index'.
            '/pageNum/'.$params['pageNum'].
            '/pageSize/'.$params['pageSize'].
            '/from_number/'.$params['fromNumber']
        );

        $json_data = json_decode($response->getBody()->getContents());

        if ($json_data->status == 'success') {
            foreach ($json_data->data->orders as $order) {
                $creating_order = Order::firstOrCreate(['order_number' => $order->id]);

                $creating_order->customer_name = $order->name;
                $creating_order->customer_email = $order->email;
                $creating_order->customer_telephone = $order->telephone;
                $creating_order->country = $order->country;
                $creating_order->city = $order->city;
                $creating_order->street = implode(',', $order->street);
                $creating_order->pincode = $order->pincode;
                $creating_order->weight = $order->weight;
                $creating_order->grand_total = $order->revenue;
                $creating_order->shipping_prices = json_encode($order->shipping_prices);
                $creating_order->save();

                $orderId = Order::find($creating_order->id)->id;

                // todo what is this line used for?
//                OrderProduct::where('order_id', '=', $orderId)->delete();

                foreach ($order->items as $item) {
                    $order = OrderProduct::whereMerchantId($item->merchant_id ?: 0)
                        ->whereOrderId($orderId)
                        ->whereSku($item->sku)
                        ->whereItemId($item->item_id)
                        ->first();

                    $purchasePrice = OrderProduct::productPrice($item->sku);
                    if ($order) {
                        $order->update([
                            'name'    => $item->name,
                            'price'   => $item->price,
                            'purchase_price' => $purchasePrice,
                            'qty'     => $item->qty,
                        ]);
                    }
                    else {
                        OrderProduct::create([
                            'merchant_id' => $item->merchant_id ?: 1,
                            'order_id'    => $orderId,
                            'sku'         => $item->sku,
                            'item_id'     => $item->item_id,
                            'name'        => $item->name,
                            'price'       => $item->price,
                            'purchase_price' => $purchasePrice,
                            'qty'         => $item->qty,
                            'status'      => OrderProduct::STATUS_PENDING,
                        ]);
                    }
                }
            }
        }

        return json_encode($json_data);
    }
}
