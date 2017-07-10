<?php

namespace App\Http\Controllers;

use App\Models\OrderProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {

        switch ($request->get('time')) {
            case 'today':
                $date = Carbon::now()->subHour(24);
                break;
            case 'yesterday':
                $date = Carbon::now()->subHours(48);
                $dateBefore = Carbon::now()->subHours(24);
                break;
            case 'week':
                $date = Carbon::now()->subWeek();
                break;
            case 'month':
            default:
                $date = Carbon::now()->subMonth();
                break;
        }

        $query = OrderProduct
            ::whereMerchantId(auth()->id())
            ->where('created_at', '>=', $date);

        if (isset($dateBefore)) {
            $query->whereDate('created_at', '<=', $dateBefore);
        }
                  
        $orders = $query->get();
        $sales = $orders->count();
        $revenue = 0;

        foreach ($orders as $order) {
            $revenue = $revenue + ((float)$order->price * (int)$order->qty);
        }

        return view('pages.reports.index', compact('orders', 'revenue', 'sales'));
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return view('pages.reports.create');
    }
}
