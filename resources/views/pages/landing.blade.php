@extends('master')

@section('content')
    <div class="page-home">
        <div class="container">
            <h1>Home</h1>

            <p>
                Welcome to Cadabra Express.
                Start listing your products today
                ! For help please contact our support team at support@cadabraexpress.com
            </p>

            <table class="table">
                <tr>
                    <td><a href="#">Connect eBay store</a></td>
                    <td><a href="#">Connect Amazon store</a></td>
                </tr>
                <tr>
                    <td><a href="#">Setup Shipping Cost Deduction</a></td>
                </tr>
            </table>

            <fieldset class="seller-tools m-t-2">
                <legend>Seller Tools</legend>

                <a href="#" class="btn btn-primary m-r-3">Add Product</a>
                <a href="#" class="btn btn-primary m-r-3">CSV Import</a>
                <a href="#" class="btn btn-primary m-r-3">Todays Sales</a>
                <a href="#" class="btn btn-primary m-r-3">View Storefront</a>
            </fieldset>

            <fieldset class="m-t-2">
                <legend>Recent Orders</legend>
                <table class="table">
                    <tr>
                        <th></th>
                        <th>Order #</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Full Name</th>
                        <th>Country</th>
                    </tr>
                    <tr>
                        <td><i class="fa fa-check"></i></td>
                        <td>test</td>
                        <td>test</td>
                        <td>test</td>
                        <td>test</td>
                        <td>test</td>
                        <td>test</td>
                    </tr>
                </table>
            </fieldset>
        </div>
    </div>
@endsection
