@extends('master')


@section('content')
    <div class="page-merchants-index">

        <div class="container-top">
            <h4>Merchants</h4>
        </div>

        <div class="container">

            <div class="text-left m-b-2">
                <a href="/merchants/create" class="btn btn-primary">Add Merchant</a>
            </div>

            <table id="merchants-table" class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Products</th>
                    <th>Verified</th>
                    <th>Disabled</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($merchants as $merchant)
                    <tr data-merchant-id="{{ $merchant->id }}">
                        <td>{{ $merchant->id }}</td>
                        <td><a href="/merchants/{{ $merchant->id }}/impersonate">{{ $merchant->name }}</a></td>
                        <td>{{ $merchant->email }}</td>
                        <td>
                            {{ $merchant->totalProducts() }}
                        </td>
                        <td>
                            <button type="button" data-is-verified="{{ $merchant->is_verified }}" data-merchant-id="{{ $merchant->id }}"
                                    class="merchant-verify btn btn-sm btn-{{ $merchant->is_verified ? 'success' : 'warning' }}-outline">
                                <i class="glyphicon {{ $merchant->is_verified ? 'glyphicon-check' : 'glyphicon-unchecked' }}"></i>
                            </button>
                        </td>
                        <td>
                            <button type="button" data-is-disabled="{{ $merchant->is_disabled }}" data-merchant-id="{{ $merchant->id }}"
                                    class="merchant-disable btn btn-sm btn-{{ $merchant->is_disabled ? 'success' : 'warning' }}-outline">
                                <i class="glyphicon {{ $merchant->is_disabled ? 'glyphicon-check' : 'glyphicon-unchecked' }}"></i>
                            </button>
                        </td>
                        <td>
                            <button type="button" class="merchant-delete btn btn-sm btn-danger-outline" data-merchant-id="{{ $merchant->id }}">
                                Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div>{!! $merchants->links() !!}</div>
        </div>
    </div>
@endsection
