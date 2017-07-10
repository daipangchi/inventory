<?php

$merchant = auth()->user();
$shippingCredentials = json_decode($merchant->shipping_credential);

?>

@extends('master')

@section('content')
    <div class="page-shipments-index">

        <div class="container-top">
            <h4>Shipments</h4>
        </div>

        <div class="container">
            <!--<h1>Shipments</h1>

            <hr>-->
            @if(isset($shippingCredentials->username) && isset($shippingCredentials->password))
            <div class="alert alert-success">
                To login, please use your username: <b>{{ $shippingCredentials->username }}</b> and password: <b>{{ $shippingCredentials->password }}</b>
            </div>
            @else
            <div class="alert alert-warning">
                Please contact Cadabra support for your shipping login credentials.
            </div>
            @endif
            
            <iframe src="https://levcargogoods.com/" frameborder="0" style="height: 800px;width:100%;"></iframe>
        </div>
    </div>
@endsection