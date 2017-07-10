<?php

$merchant = auth()->user();
$shippingCredentials = json_decode($merchant->shipping_credential);

?>

@extends('master')

@section('content')
    <?php
    $pendingCode = \App\Models\OrderProduct::STATUS_PENDING;
    $approvedCode = \App\Models\OrderProduct::STATUS_APPROVED;
    $rejectedCode = \App\Models\OrderProduct::STATUS_REJECTED;
    $statuses = Request::get('statuses') ?: [];
    ?>

    <div class="page-orders-index">

        <div class="container-top">
            <h4>Orders</h4>
        </div>

        <div class="container">
            
            @if(!isset($shippingCredentials->username) || !isset($shippingCredentials->password))
            <div class="alert alert-warning">
                You cannot approve orders. Please contact Cadabra Support for your shipping login credentials.
            </div>
            @endif

            <form action="/orders" method="get">
                <div class="row">
                    <div class="col-xs-12 col-sm-3">
                        <div class="form-group">
                            <div class="input-group">
                                <input name="search_number" type="text" class="form-control"
                                       placeholder="Search by order #"
                                       value="{{ Request::get('search_number') }}">
                                <div class="input-group-btn">
                                    <button class="btn btn-secondary" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-3">
                        <div class="form-group">
                            <div class="input-group">
                                <input name="search_name" type="text" class="form-control" placeholder="Search by item name" value="{{ Request::get('search_name') }}">
                                <div class="input-group-btn">
                                    <button class="btn btn-secondary" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($shippingCredentials->username) && isset($shippingCredentials->password))
                    <div class="col-xs-12 col-sm-6 text-right">
                        @if(!auth()->user()->is_admin)
                        <button type="button" id="button-approve-orders" class="btn btn-primary m-r-2">Approve Orders</button>
                        @endif
                        
                        <button type="button" id="button-reject-orders" class="btn btn-secondary">Reject Orders</button>
                    </div>
                    @else
                    <div class="col-xs-12 col-sm-6 text-right">
                        <button type="button" class="btn btn-primary m-r-2 disabled" disabled="disabled">Approve Orders
                        </button>
                        <button type="button" class="btn btn-secondary disabled" disabled="disabled">Reject Orders</button>
                    </div>
                    @endif
                    
                </div>

                <div class="row m-t-2 m-b-2">
                    <div class="col-xs-12">
                        <select name="statuses[]" multiple class="hidden" aria-hidden="true">
                            <option value="{{ $pendingCode }}" {{ in_array($pendingCode, $statuses) ? 'selected' : '' }}>{{ $pendingCode }}</option>
                            <option value="{{ $approvedCode }}" {{ in_array($approvedCode, $statuses) ? 'selected' : '' }}>{{ $approvedCode }}</option>
                            <option value="{{ $rejectedCode }}" {{ in_array($rejectedCode, $statuses) ? 'selected' : '' }}>{{ $rejectedCode }}</option>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <label class="checkbox-inline">
                            <input class="checkbox-status" type="checkbox" value="{{ $pendingCode }}"
                                   {{ in_array($pendingCode, $statuses) || ! str_contains(Request::fullUrl(), '?') ? 'checked' : '' }}>
                            Pending ({{ $pendingCount }})
                        </label>

                        <label class="checkbox-inline">
                            <input class="checkbox-status" type="checkbox" value="{{ $approvedCode }}"
                                    {{ in_array($approvedCode, $statuses) ? 'checked' : '' }}>
                            Approved ({{ $approvedCount }})
                        </label>

                        <label class="checkbox-inline">
                            <input class="checkbox-status" type="checkbox" value="{{ $rejectedCode }}"
                                    {{ in_array($rejectedCode, $statuses) ? 'checked' : '' }}>
                            Rejected ({{ $rejectedCount }})
                        </label>
                    </div>
                    <div class="col-sm-5">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="input-group date datetimepicker-1">
                                    <input type="text" name="date_start" class="form-control form-control-sm" placeholder="Start"
                                           value="{{ Request::get('date_start') }}">
                                    <span class="input-group-addon">
                                        <i class="font-icon font-icon-calend"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="input-group date datetimepicker-1">
                                    <input type="text" name="date_end" class="form-control form-control-sm" placeholder="End"
                                           value="{{ Request::get('date_end') }}">
                                    <span class="input-group-addon">
                                        <i class="font-icon font-icon-calend"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-1 text-right">
                        <button type="submit" class="btn btn-default btn-sm">Filter</button>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-hover m-t-2">
                <thead>
                <tr>
                    <th><input type="checkbox" id="checkbox-action-all"></th>
                    <th>Order #</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Items</th>
                    <th>Full Name</th>
                    <th>Country</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><input type="checkbox" class="checkbox-select-order" data-order-id="{{ $order->id }}"></td>
                        <td><a href="/orders/{{ $order->order_number }}">{{ $order->order_number }}</a></td>
                        <td>
                            {{ $order->created_at->format('m/d/Y') }}<br>
                            {{ $order->created_at->format('g:i a') }}
                        </td>
                        <td>
                            {{ $order->status == \App\Models\OrderProduct::STATUS_PENDING ? 'Pending' : '' }}
                            {{ $order->status == \App\Models\OrderProduct::STATUS_APPROVED ? 'Approved' : '' }}
                            {{ $order->status == \App\Models\OrderProduct::STATUS_REJECTED ? 'Rejected' : '' }}
                        </td>
                        <td>
                            @foreach($order->items as $i => $item)
                                <span class="order_item" data-order-product-id="{{ $item->id }}">
                                    <strong>{{ $item->qty }}x</strong>
                                    {{ $item->name }}
                                </span>

                                @if($i != count($order->items) - 1)<br>@endif
                            @endforeach
                        </td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ COUNTRIES[$order->country] ?? '?' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">No orders found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div>{{ $orders->links() }}</div>
        </div>
    </div>
@endsection