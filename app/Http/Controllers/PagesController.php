<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderProduct;
use Carbon\Carbon;

class PagesController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        $orderProductIds = OrderProduct::whereMerchantId(auth()->id())
            ->whereDate('created_at', '>', Carbon::now()->subDays(7)->getTimestamp())
            ->pluck('order_id');

        $orders = Order::with('items')->whereIn('id', $orderProductIds)->get();

        return view('pages.home', compact('orders'));
    }
}
