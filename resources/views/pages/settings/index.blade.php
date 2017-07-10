@extends('master')

@section('content')
    <div class="page-settings">

        <div class="container-top">
            <h4>Settings</h4>
        </div>

        <div class="container">

            <div class="col-sm-6">
                <!-------------------------------------------------------------------
                    EBAY
                -------------------------------------------------------------------->
                <fieldset>
                    <legend>eBay Import Configuration</legend>

                    <div class="ebay-validations-container"></div>

                    @if(auth()->user()->isConnectedTo('ebay'))
                        <button type="button" class="btn btn-success disabled" disabled>
                            Connected
                        </button>
                        <button type="button" class="btn btn-danger-outline m-l-2" id="button-disconnect-ebay" data-channel="ebay">
                            Disconnect eBay
                        </button>
                    @else
                        <button type="button" class="btn btn-primary" id="link-connect-ebay">
                            Connect eBay Account
                        </button>
                    @endif
                    <p class="m-t-1">Products added manually on this website will not sync with eBay.</p>

                    <form class="ebay" action="/merchants/{{ auth()->id() }}" method="post">
                        {!! csrf_field() !!}

                        <p>Automatically discount by percentage when importing.</p>

                        <div class="p-l-3">
                            <div class="input-group input-group-sm discount-amount-input-group">
                                <input type="text" class="form-control" placeholder="0"
                                       name="ebay_import_options[discount_amount]"
                                       value="{{ $ebayOptions['discount_amount'] ?? '' }}">
                                <div class="input-group-addon">%</div>
                            </div>
                        </div>

                        <div class="m-t-1">
                            <div class="checkbox">
                                <input type="hidden" name="ebay_import_options[remove_sold_items]" value="false">
                                <input type="checkbox" id="ebay_check_sold"
                                       name="ebay_import_options[remove_sold_items]"
                                       value="true" {{ $ebayOptions['remove_sold_items'] ? 'checked' : '' }}>
                                <label for="ebay_check_sold">Remove if item is <strong>sold</strong>.</label>
                            </div>

                            <div class="checkbox">
                                <input type="hidden" name="ebay_import_options[remove_ended_items]" value="false">
                                <input type="checkbox" id="ebay_check_ended"
                                       name="ebay_import_options[remove_ended_items]"
                                       value="true" {{ $ebayOptions['remove_ended_items'] ? 'checked' : '' }}>
                                <label for="ebay_check_ended">Remove if item is <strong>ended</strong>.</label>
                            </div>

                            <div class="checkbox">
                                <input type="hidden" name="ebay_import_options[remove_removed_items]" value="false">
                                <input type="checkbox" id="ebay_check_removed"
                                       name="ebay_import_options[remove_removed_items]"
                                       value="true" {{ $ebayOptions['remove_removed_items'] ? 'checked' : '' }}>
                                <label for="ebay_check_removed">Remove if item is <strong>removed</strong>.</label>
                            </div>
                            
                            @if(auth()->user()->isConnectedTo('ebay'))
                            <div class="checkbox">
                                <button type="submit" class="btn btn-info" id="update-ebay-setting">
                                    Update eBay Settings
                                </button>
                            </div>
                            @endif
                        </div>
                    </form>
                </fieldset>

                <!-------------------------------------------------------------------
                    AMAZON
                -------------------------------------------------------------------->
                <fieldset class="m-t-2">
                    <legend>Amazon Import Configuration</legend>

                    @if(!auth()->user()->isConnectedTo('amazon'))
                        <p>
                            Application name: CommerceTrade<br>
                            Developer Account Number: 2908-0569-0084
                        </p>
                    @endif

                    @if(auth()->user()->isConnectedTo('amazon'))
                        <a href class="btn btn-success disabled" disabled>Connected</a>
                        <button type="button" class="btn btn-danger-outline m-l-2" id="button-disconnect-amazon" data-channel="amazon">
                            Disconnect Amazon
                        </button>
                    @else
                        <a href="https://sellercentral.amazon.com/gp/mws/registration/register.html?ie=UTF8&*Version*=1&*entries*=0&signInPageDisplayed=1"
                           class="btn btn-primary-outline" target="_blank">Get API Token</a>
                    @endif
                    
                    <p class="m-t-1">Products added manually on this website will not sync with Amazon.</p>

                    <form class="amazon" action="/settings/amazon/connect" method="post">
                        @include('common.errors')
                        {{ csrf_field() }}
                        <div class="form-group row {{ $errors->has('amazon_seller_id') }}">
                            <label class="col-sm-4 form-control-label">Seller ID</label>
                            <div class="col-sm-8">
                                <p class="form-control-static form-control-sm">
                                    <input type="text"
                                           class="form-control"
                                           name="amazon_seller_id"
                                           placeholder="Seller ID"
                                           value="{{ old('amazon_seller_id') ? old('amazon_seller_id') : auth()->user()->amazon_seller_id }}">
                                </p>
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('amazon_auth_token') }}">
                            <label class="col-sm-4 form-control-label">MWS Auth Token</label>
                            <div class="col-sm-8">
                                <p class="form-control-static form-control-sm">
                                    <input type="text"
                                           class="form-control"
                                           name="amazon_auth_token"
                                           placeholder="MWS Auth Token"
                                           value="{{ old('amazon_auth_token') ? old('amazon_seller_id') : auth()->user()->amazon_auth_token }}">
                                </p>
                            </div>
                        </div>

                        <p>Automatically discount by percentage when importing.</p>

                        <div class="p-l-3">
                            <div class="input-group input-group-sm discount-amount-input-group">
                                <input type="text" class="form-control" placeholder="0"
                                       name="amazon_import_options[discount_amount]"
                                       value="{{ $amazonOptions['discount_amount'] ?? '' }}">
                                <div class="input-group-addon">%</div>
                            </div>
                        </div>

                        <div class="m-t-1">
                            <div class="checkbox">
                                <input type="hidden" name="amazon_import_options[remove_sold_items]" value="false">
                                <input type="checkbox" id="amazon_check_sold"
                                       name="amazon_import_options[remove_sold_items]"
                                       value="true" {{ isset($amazonOptions['remove_sold_items']) && $amazonOptions['remove_sold_items'] ? 'checked="checked"' : '' }}>
                                <label for="amazon_check_sold">Remove if item is <strong>sold</strong>.</label>
                            </div>

                            <div class="checkbox">
                                <input type="hidden" name="amazon_import_options[remove_ended_items]" value="false">
                                <input type="checkbox" id="amazon_check_ended"
                                       name="amazon_import_options[remove_ended_items]"
                                       value="true" {{ isset($amazonOptions['remove_ended_items']) && $amazonOptions['remove_ended_items'] ? 'checked="checked"' : '' }}>
                                <label for="amazon_check_ended">Remove if item is <strong>ended</strong>.</label>
                            </div>

                            <div class="checkbox">
                                <input type="hidden" name="amazon_import_options[remove_removed_items]"
                                       value="false">
                                <input type="checkbox" id="amazon_check_removed"
                                       name="amazon_import_options[remove_removed_items]"
                                       value="true" {{ isset($amazonOptions['remove_removed_items']) && $amazonOptions['remove_removed_items'] ? 'checked="checked"' : '' }}>
                                <label for="amazon_check_removed">Remove if item is
                                    <strong>removed</strong>.</label>
                            </div>
                        </div>

                        @if(auth()->user()->isConnectedTo('amazon'))
                        <div class="checkbox">
                            <button type="submit" class="btn btn-info" id="update-ebay-setting">
                                Update eBay Settings
                            </button>
                        </div>
                        @else
                        <div class="checkbox">
                            <button class="btn btn-primary m-t">Connect</button>
                        </div>    
                        @endif
                    </form>

                </fieldset>
            </div>

            <div class="col-sm-6">
                <fieldset>
                    <legend>Shipping Cost Deduction</legend>

                    <p>
                        You can insert your shipping rates based on weight to automatically deduct your costs when we
                        import your products.
                    </p>

                    <div class="text-right">
                        <button class="btn btn-sm btn-primary-outline" data-toggle="collapse"
                                data-target="#deduction-form">Add Deduction
                        </button>
                    </div>

                    <div>
                        <div class="deductions-validations-container m-t-1"></div>

                        <form id="deduction-form" class="row m-t-1 collapse" action="/settings/deductions"
                              method="post">
                            {{csrf_field()}}

                            <div class="col-sm-3 form-group form-group-sm">
                                <label class="sr-only" for="from_weight">Email address</label>
                                <input type="number" class="form-control form-control-sm" id="from_weight"
                                       name="from_weight" placeholder="From"
                                       data-toggle="tooltip" data-placement="top" title="From weight (lbs)" step="any">
                            </div>

                            <div class="col-sm-3 form-group form-group-sm p-l-0">
                                <label class="sr-only" for="to_weight">Password</label>
                                <input type="number" class="form-control form-control-sm" id="to_weight"
                                       name="to_weight" placeholder="To"
                                       data-toggle="tooltip" data-placement="top" title="To weight (lbs)" step="any">
                            </div>

                            <div class="col-sm-3 form-group form-group-sm p-l-0">
                                <label class="sr-only" for="amount">Password</label>
                                <input type="number" class="form-control form-control-sm" id="amount" name="amount"
                                       placeholder="Deduction" data-toggle="tooltip" data-placement="top"
                                       title="Deduction amount (USD)" step="any">
                            </div>

                            <div class="col-sm-3 form-group form-group-smp-l-0">
                                <button type="submit" class="btn btn-primary btn-sm btn-block">Save</button>
                            </div>
                        </form>
                    </div>

                    <table id="deductions-table" class="table m-t-1 {{ !$importDeductions->count() ? 'hidden' : ''}}">
                        <thead>
                        <tr>
                            <th>From Weight</th>
                            <th>To Weight</th>
                            <th>Deduct</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="hidden deduction-template">
                            <td class="deduction-from"></td>
                            <td class="deduction-to"></td>
                            <td class="deduction-amount"></td>
                            <td><a href class="link-delete-deduction">Delete</a></td>
                        </tr>
                        @foreach($importDeductions as $deduction)
                            <tr>
                                <td class="deduction-from">{{ number_format($deduction->from_weight, 2) }} lbs</td>
                                <td class="deduction-to">{{ number_format($deduction->to_weight, 2) }} lbs</td>
                                <td class="deduction-amount">${{ number_format($deduction->amount, 2) }}</td>
                                <td><a href class="link-delete-deduction" data-deduction-id="{{ $deduction->id }}">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </div>
    </div>
@endsection
