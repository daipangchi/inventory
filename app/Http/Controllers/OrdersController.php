<?php

namespace App\Http\Controllers;

use App\Apis\Levcargo\Client;
use App\Models\Merchants\Merchant;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Products\Product;
use Artisan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * @var Client
     */
    protected $levClient;


    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $orderItems = $this->getOrderItems($request);

        $pendingCount = OrderProduct::whereMerchantId(auth()->id())->whereStatus(OrderProduct::STATUS_PENDING)->count();
        $approvedCount = OrderProduct::whereMerchantId(auth()->id())->whereStatus(OrderProduct::STATUS_APPROVED)->count();
        $rejectedCount = OrderProduct::whereMerchantId(auth()->id())->whereStatus(OrderProduct::STATUS_REJECTED)->count();

        $ordersQuery = Order::with(['items' => function ($q) {
            if (! auth()->user()->is_admin) {
                $q->whereMerchantId(auth()->id());
            }
        }]);

        if ($number = $request->get('search_number')) {
            $ordersQuery->where('order_number', 'like', "%$number%");
        }

        if ($name = $request->get('search_name')) {
            $items = OrderProduct::whereMerchantId(auth()->id())
                ->where('name', 'like', "%$name%")
                ->pluck('order_id');

            $ordersQuery->whereIn('id', $items->toArray());
        }
        else {
            $ordersQuery->whereIn('id', $orderItems->pluck('order_id'));
        }

        if ($startDate = $request->get('date_start')) {
            $ordersQuery->whereDate('created_at', '>', $this->parseDate($startDate));
        }

        if ($endDate = $request->get('date_end')) {
            $ordersQuery->whereDate('created_at', '<', $this->parseDate($endDate));
        }

        $orders = $ordersQuery->paginate();

        return view('pages.orders.index', compact(
            'orders',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return view('pages.orders.create');
    }

    /**
     * @param $orderNumber
     * @return mixed
     */
    public function show($orderNumber)
    {
        $query = Order::with(['items' => function ($q) {
            if (! auth()->user()->is_admin) {
                $q->whereMerchantId(auth()->id());
            }
        }])->whereOrderNumber($orderNumber);

        /*if (! auth()->user()->is_admin) {
            $query->whereMerchantId(auth()->id());
        }*/

        $order = $query->firstOrFail();

        return view('pages.orders.show', compact('order'));
    }

    /**
     * @param Request $request
     * @return string
     */
    public function update(Request $request)
    {
        $merchantId = auth()->id();
        $merchant = auth()->user();
        $shippingCredentials = json_decode($merchant->shipping_credential);
        if(!isset($shippingCredentials->username) || !isset($shippingCredentials->password)) {
            return 'bad';
        }
        config([
            'services.levcargo.username' => $shippingCredentials->username,
            'services.levcargo.password' => $shippingCredentials->password
        ]);
        
        
        $orderItems = OrderProduct::whereMerchantId($merchantId)
            ->whereIn('id', $request->get('orderProductIds'))->get();

        if (! $orderItems->count()) {
            return response(['error' => ['Couldn\'t not find items.']], 422);
        }

        $order = Order::whereId($orderItems[0]->order_id)->first();

        switch ($request->get('action')) {
            case 'approve':
                $this->approveOrder($order, $orderItems);
                $status = OrderProduct::STATUS_APPROVED;
                break;
            case 'reject':
                $this->declineOrder($order, $orderItems);
                $status = OrderProduct::STATUS_REJECTED;
                break;
            default:
                $status = OrderProduct::STATUS_PENDING;
                break;
        }

        foreach ($orderItems as $orderItem) {
            $orderItem->update(compact('status'));
        }

        return 'good';
    }

    /**
     * @param Order $order
     * @param $orderItems
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function placeLevcargoOrder(Order $order, $orderItems)
    {
        $merchant = auth()->user();
        $shippingCredentials = json_decode($merchant->shipping_credential);
        
        $fullName = explode(' ', $order->customer_name);
        $customerFirstName = array_first($fullName);
        $customerLastName = count($fullName) > 1 ? array_last($fullName) : '';

        $params = [
            "ReferenceId"   => "351",
            "Customer"      => [
                "LastName"    => $customerLastName,
                "FirstName"   => $customerFirstName,
                /*"Phone"       => "0506890881",
                "IdNumber"    => "033491952",
                "Email"       => $order->customer_email,
                "City"        => "תל אביב",
                "CityCode"    => "",
                "HouseNumber" => "1",
                "Street"      => "רוטשילד",
                "StreetCode"  => "",*/
                "Phone"       => $order->customer_telephone,
                "IdNumber"    => "033491952",
                "Email"       => $order->customer_email,
                "City"        => $order->city,
                "CityCode"    => $order->pincode,
                "HouseNumber" => "1",
                "Street"      => $order->street,
                "StreetCode"  => "",
                "ReferenceId" => null,
            ],
            "Items"         => [],
            //"VendorEmail"   => "Alon@shoezoo.com",
            "VendorEmail"   => $shippingCredentials->username,
            "ShippingPrice" => $order->getShippingPrice(auth()->id()),
            "TotalTax"      => 0,
            "TaxPercent"    => 29,
        ];

        /* @var $orderItem OrderProduct */
        foreach ($orderItems as $orderItem) {
            $params['Items'][] = [
                'Product'     => [
                    'CreatedAt'               => $orderItem->created_at->format(DATE_ISO8601),
                    'ReferenceId'             => '17763',
                    'CategoryId'              => 0,
                    'Name'                    => $orderItem->name ?: 'test name',
                    'Sku'                     => $orderItem->sku ?: 'sku-test',
                    'ManufacturerSku'         => null,
                    'CategoryCode'            => '47686',
                    'TaxCategoryCode'         => '6',
                    'ManufacturerReferenceId' => null,
                    'SerialNumber'            => null,
                    'ManufacturerId'          => 0,
                    'Description'             => $orderItem->name,
                    'VendorPrice'             => $orderItem->purchase_price,
                    'CustomerPrice'           => $orderItem->price,
                    'Id'                      => 0,
                    'Width'                   => 1,
                    'Height'                  => 1,
                    'Length'                  => 1,
                    'Location'                => null,
                ],
                'Price'       => $orderItem->price ?: 1,
                'VendorPrice' => $orderItem->purchase_price ?: 1,
                'Quantity'    => $orderItem->qty ?: 1,
                'HKCode'      => $orderItem->getCustomCode(),
                'Weight'      => (isset($orderItem->weight) && ($orderItem->weight > 0)) ? $orderItem->weight : 1,
            ];
        }

        $levCargoClient = new Client();

        return $levCargoClient->authenticate()->placeOrder($params);
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Database\Eloquent\Collection|static[]
     */
    protected function getOrderItems(Request $request)
    {
        $orderItems = OrderProduct::whereMerchantId(auth()->id());

        // Hackish way to get only products with status pending on first page load.
        if (! str_contains($request->fullUrl(), '?')) {
            $orderItems->whereStatus(OrderProduct::STATUS_PENDING);
        }
        else {
            if($request->has('statuses')) {
                $orderItems->whereIn('status', $request->get('statuses') ?: []);
            }
        }

        return $orderItems->get();
    }

    /**
     * @param $date
     * @return string
     */
    protected function parseDate($date)
    {
        // 09/22/2016 12:00 AM
        $date = substr($date, 0, strpos($date, ' '));

        list($m, $d, $y) = explode('/', $date);

        return Carbon::createFromFormat('Y-m-d', "$y-$m-$d")->format('y-m-d h:i:s');
    }

    /**
     * @param $order
     * @param $orderItems
     */
    protected function approveOrder($order, $orderItems)
    {
        $response = $this->placeLevcargoOrder($order, $orderItems);

        if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) {
            Artisan::call('order:push', [
                'order_number'  => $order->order_number,
                'merchant_id'   => $orderItems[0]->merchant_id,
                'order_command' => 'approve',
            ]);
        }
        else {
            abort(500, 'Failed');
        }

        // Lower product quantity by the amount that was ordered
        foreach ($orderItems as $item) {
            $product = Product::whereMerchantId(auth()->id())->whereSku($item->sku)->first();

            if ($product) {
                $product->decrement('quantity', $item->qty);
            }
        }
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $order
    * @param mixed $orderItems
    */
    protected function declineOrder($order, $orderItems) 
    {
        Artisan::call('order:push', [
            'order_number'  => $order->order_number,
            'merchant_id'   => $orderItems[0]->merchant_id,
            'order_command' => 'decline',
        ]);   
    }
}
