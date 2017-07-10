@extends('master')

@section('content')
    <div class="page-inventory-csv-categories">

        <div class="container-top">
            <h4>Category Mapping</h4>
        </div>


        <div class="container">

            <div class="row">
                <div class="col-sm-6">
                    <form action="/inventory/csv/categories" class="input-group">
                        <input type="text" class="form-control" placeholder="Search" name="search">
                        <span class="input-group-btn">
                            <button class="btn btn-secondary" type="button"><i class="glyphicon glyphicon-search"></i></button>
                        </span>
                    </form>
                </div>
            </div>

            <table class="table m-t-2" id="categories-table">
                <colgroup>
                    <col width="20%">
                    <col width="80%">
                </colgroup>
                <thead>
                <th>Category ID</th>
                <th>Name</th>
                </thead>

                <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->category_id }}</td>
                        <td>{{ $category->path_by_name }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div>{!! $categories->links() !!}</div>
        </div>
    </div>
@endsection
