@extends('master')
                    
@section('content')
    <div class="page-reports-index">

        <div class="container-top">
            <h4>Reports</h4>
        </div>

        <div class="container">
            <h3>
                Time Period
                <div class="btn-group">
                    <i class="glyphicon glyphicon-arrow-down btn btn-primary-outline dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></i>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="/reports?time=today">Today</a>
                        <a class="dropdown-item" href="/reports?time=yesterday">Yesterday</a>
                        <a class="dropdown-item" href="/reports?time=week">Week</a>
                        <a class="dropdown-item" href="/reports?time=month">Month</a>
                    </div>
                </div>
            </h3>

            <div class="row">
                <div class="col-sm-3">
                    <fieldset class="total-sales">
                        <legend>Total Sales</legend>

                        <p class="text-center">{{ $sales }}</p>
                    </fieldset>
                </div>
                <div class="col-sm-3">
                    <fieldset class="total-revenue">
                        <legend>Total Revenue</legend>

                        <p class="text-center">${{ number_format($revenue, 2) }}</p>
                    </fieldset>
                </div>
            </div>

            <table class="table m-t-3">
                <thead>
                <th>Order #</th>
                <th>Customer Name</th>
                <th>Total Items</th>
                <th>Total Revenue</th>
                <th>Created</th>
                </thead>
                @forelse($orders as $order)
                    @if(isset($order->order->order_number)) 
                    <tr>
                        <td><a href="/orders/{{ $order->order->order_number }}">{{ $order->order->order_number }}</a></td>
                        <td>{{ $order->order->customer_name }}</td>
                        <td>{{ $order->order->total_quantity }}</td>
                        <td>${{ $order->qty * $order->price }}</td>
                        <td>{{ $order->created_at }}</td>
                    </tr>
                    @endif
                @empty
                    <tr>
                        <td>No orders</td>
                    </tr>
                @endforelse
            </table>
        </div>
    </div>
@endsection
