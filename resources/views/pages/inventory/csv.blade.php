@extends('master')

@section('content')
    <div class="page-inventory-csv">

        <div class="container-top">
            <h4>Import CSV</h4>
        </div>

        <div class="container">

            <form id="csv-import-form" enctype="multipart/form-data" method="post" action="#">
                {{ csrf_field() }}

                <p><a href="/inventory/csv/categories">View Categories</a></p>
                <p><a href="/products.csv">Download template file</a></p>

                <hr>

                <div>
                    <p id="selected-file"></p>

                    <input id="csv-input" name="file" type="file" class="hidden">

                    <button id="csv-import-button" type="button" class="btn btn-primary-outline m-r-3">
                        Select CSV
                    </button>

                    <button type="submit" class="btn btn-primary">Import</button>
                </div>

            </form>
        </div>
    </div>
@endsection
