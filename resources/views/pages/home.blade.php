@extends('master')

@section('content')
    <div class="page-home" style="background: none">

        <div class="container-top">
            <h4>Home Page</h4>
        </div>

        <div class="container">

            <p>
                <h3 class="with-border">Welcome to Cadabra Express</h3>

                <div class="alert alert-aquamarine alert-no-border alert-close alert-dismissible fade in" role="alert">
                    <strong>Important Next Steps</strong><br>
                    Connect your eBay or Amazon store and configure your shipping cost deductions.
                    <a href="settings" class="btn btn-sm">Configure</a>
                </div>

                For help please contact our support team at
                <a href="mailto:support@cadabraexpress.com">support@cadabraexpress.com</a>
            </p>

            <fieldset class="seller-tools m-t-2">
                <legend>Seller Tools</legend>

                <a href="/inventory/create" class="btn btn-primary m-r-3">Add Product</a>
                <a href="/inventory/csv" class="btn btn-primary m-r-3">CSV Import</a>
                <a href="/reports" class="btn btn-primary m-r-3">Todays Sales</a>
            </fieldset>

            <fieldset class="m-t-2">
                <legend>Recent Orders</legend>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Full Name</th>
                        <th>Country</th>
                    </tr>
                    </thead>
                    @forelse($orders as $order)
                    <tr>
                        <td><a href="/orders/{{ $order->order_number }}">{{ $order->order_number }}</a></td>
                        <td>
                            {{ $order->created_at->format('m/d/Y') }}<br>
                            {{ $order->created_at->format('g:i a') }}
                        </td>
                        <td>
                            @if($order->status == \App\Models\OrderProduct::STATUS_APPROVED)
                                Approved
                            @elseif($order->status == \App\Models\OrderProduct::STATUS_REJECTED)
                                Rejected
                            @elseif($order->status == \App\Models\OrderProduct::STATUS_PENDING)
                                Pending
                            @endif
                        </td>
                        <td>
                            <?php 
                            $items = array();
                            if (auth()->user()->is_admin) {
                                $items = $order->items;
                            } else {
                                $items = $order->items->where('merchant_id', auth()->id());
                            }
                            ?>
                            @foreach($items as $item)
                                <p style="margin-bottom: 4px;">{{ $item->qty }}x - {{ $item->name }}</p>
                            @endforeach
                        </td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->country }}</td>
                    </tr>
                    @empty
                        <tr>
                            <td>No new orders yet...</td>
                        </tr>
                    @endforelse
                </table>
            </fieldset>
        </div>
    </div>
@endsection
