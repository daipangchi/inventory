@extends('master')

@section('content')
    <div class="page-stub-show">

        <div class="container-top">
            <h4>View Order #{{ $order->order_number }}</h4>
        </div>

        <div class="container">

            @if($order->status == \App\Models\OrderProduct::STATUS_APPROVED)
                <p>Status: Approved</p>
            @elseif($order->status == \App\Models\OrderProduct::STATUS_REJECTED)
                <p>Status: Rejected</p>
            @elseif($order->status == \App\Models\OrderProduct::STATUS_PENDING)
                <p>Status: Pending</p>
            @endif

            <p>
                Grand Total: ${{ $order->merchant_total }}<!--${{ (auth()->user()->is_admin) ? $order->grand_total : $order->merchant_total }}-->
            </p>
            <p>Tracking #: ${{ $order->tracking_number }}</p>

            <hr>
            {{--<pre>{{ print_r($order->toArray(), true) }}</pre>--}}

            <div class="row">
                <div class="col-sm-6">
                    <h3>Items:</h3>

                    @foreach($order->items as $item)
                        <p><strong>{{$item->qty}}x</strong> - {{$item->name}}</p>
                    @endforeach
                </div>
                <div class="col-sm-6">
                    <h3>Customer:</h3>

                    <p>Name: {{ $order->customer_name }}</p>
                    <p>Email: {{ $order->customer_email }}</p>
                    <p>Phone #: {{ $order->customer_telephone }}</p>
                    <p>Pin: {{ $order->pincode }}</p>
                    <h4>Address:</h4>
                    <p>Street: {{ $order->street }}</p>
                    <p>City: {{ $order->city }}</p>
                    <p>Country: {{ $order->country }}</p>
                </div>
            </div>

        </div>
    </div>
@endsection
