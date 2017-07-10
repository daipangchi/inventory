<?php
use App\Models\Products\Product;
$status = (Request::has('status') && Request::has('status') != 0) ? Request::get('status') : '-1';
?>

@extends('master')

@section('content')
    <div class="page-inventory-index">

        <div class="container-top">
            <h4>Inventory</h4>
        </div>

        <div class="container">
            <h5>Products <span style="font-size:70%;">( {{ $totalCount }} Products )</span></h5>

            <div class="row m-b-2">
                <form action="/inventory" id="inventory-form" class="col-sm-6">
                    <input type="hidden" name="sort" value="{{ Request::get('sort') }}">
                    <input type="hidden" name="sort_direction" value="{{ Request::get('sort_direction') }}">
                    
                    <div class="col-sm-8">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search" name="search" value="{{ old('search') }}">
                            <span class="input-group-btn">
                                <button class="btn btn-secondary" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <select id="status" name="status" class="form-control">
                            <option value="">All</option>
                            <option value="{{ Product::STATUS_PENDING }}" {{ $status == Product::STATUS_PENDING ? 'selected' : '' }}>Pending</option>
                            <option value="{{ Product::STATUS_PROCESSING }}" {{ $status == Product::STATUS_PROCESSING ? 'selected' : '' }}>Processing</option>
                            <option value="{{ Product::STATUS_ACTIVE }}" {{ $status == Product::STATUS_ACTIVE ? 'selected' : '' }}>Active</option>
                            <option value="{{ Product::STATUS_PUBLISHED }}" {{ $status == Product::STATUS_PUBLISHED ? 'selected' : '' }}>Published</option>
                            <option value="{{ Product::STATUS_REMOVED }}" {{ $status == Product::STATUS_REMOVED ? 'selected' : '' }}>Removed</option>
                            <option value="{{ Product::STATUS_DISABLED }}" {{ $status == Product::STATUS_DISABLED ? 'selected' : '' }}>Disabled</option>
                        </select>
                    </div>
                </form>
                <div class="col-sm-6 text-right">
                    <a href="/inventory/csv" class="btn btn-primary-outline">CSV Import</a>
                    <a href="/inventory/create" class="btn btn-primary">Add Product</a>
                </div>
            </div>

            <table id="inventory-table" class="table table-bordered table-hover m-t-2">
                <colgroup>
                    <col width="40">
                    <!--<col width="46">-->
                    <col width="">
                    <col width="120">
                    <col width="120">
                    
                    @if(is_admin2(auth()->user()))
                    <col width="120">
                    @endif
                    
                    <col width="80">
                    <col width="80">
                    
                    @if(is_admin(auth()->user()))
                    <col width="80">
                    @endif
                    <col width="100">
                    <col width="150">
                </colgroup>
                <thead>
                <tr>
                    <th></th>
                    <!--<th></th>-->
                    <th data-sort-by="name" class="column_name">Name</th>
                    <th data-sort-by="original_price" class="text-left">Original Price</th>
                    <th data-sort-by="price" class="text-left">Price</th>
                    
                    @if(is_admin2(auth()->user()))
                    <th data-sort-by="publish_price" class="">Publish Price</th>
                    @endif
                    
                    <th data-sort-by="quantity" class="">Quantity</th>
                    <th data-sort-by="condition" class="">Condition</th>
                    <!--<th data-sort-by="variants" class="">Variants</th>
                    <th data-sort-by="updated_at" class="">Updated</th>-->
                    <th data-sort-by="channel" class="">Channel</th>

                    @if(is_admin(auth()->user()))
                        <th data-sort-by="merchant_id">Merchant</th>
                    @endif
                    <th data-sort-by="status" class="text-center">Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <?php
                        $statusResult = $product->getStatusResult();
                    ?>
                    <tr>
                        <td>
                            <a href="/inventory/{{ $product->id }}/edit">
                                @if(isset($product->images[0]))
                                    <img width="30" height="30" src="{{ $product->images[0]->url }}" alt="{{ $product->name }}">
                                @else
                                    <img width="30" height="30" src="/product-placeholder.png" alt="{{ $product->name }}">
                                @endif
                            </a>
                        </td>
                        <!--<td>
                            @if($product->type == 'configurable')
                                <i class="glyphicon glyphicon-random"></i>
                            @else
                                {{--<i class="glyphicon glyphicon-arrow-right"></i>--}}
                            @endif
                        </td> -->
                        <td>
                            <a href="/inventory/{{ $product->id }}/edit">{{ $product->name }}</a>
                        </td>
                        <td class="text-right" style="text-align: left;">
                            <div class="font-11 color-blue-grey-lighter uppercase">Original Price</div>
                            {{ money_format('$%i', $product->original_price) }}
                        </td>
                        <td class="text-right" style="text-align: left;">
                            <div class="font-11 color-blue-grey-lighter uppercase">Price</div>
                            {{ money_format('$%i', $product->price) }}
                            <span class="hint-circle purple" data-toggle="tooltip" data-placement="top" title="" data-original-title="{{ $product->priceDeductionHtml() }}">?</span>
                        </td>
                        
                        @if(is_admin2(auth()->user()))
                            <td class="text-center" style="text-align: left;">
                                <div class="font-11 color-blue-grey-lighter uppercase">Publish Price</div>
                                {{ money_format('$%i', $product->publish_price) }}</td>
                        @endif
                        
                        <td class="text-center" style="text-align: left;">
                            <div class="font-11 color-blue-grey-lighter uppercase">Quantity</div>
                            {{ $product->quantity }}
                        </td>
                        <td class="text-center" style="text-align: left;">
                            {{ ucfirst($product->condition) }}
                        </td>
                        <!--<td class="text-center">{{ $product->variants }}</td>
                        <td>
                            {{ $product->created_at->format('m/d/Y') }}<br>
                            {{ $product->created_at->format('g:i a') }}
                        </td>-->
                        <td>{{ ucfirst($product->channel) }}</td>

                        @if(is_admin(auth()->user()))
                            <td class="text-center">{{ $product->merchant_id }}</td>
                        @endif
                        <td class="text-center">
                            <div class="progress-steps-block" >
                                @if($product->isDisabled()) 
                                    <div class="progress-steps-caption">
                                        {{ $statusResult['label'] }}
                                        
                                        <small>
                                            <a href="{{ route('inventory.product.enable', array('id' => $product->id)) }}">Enable</a>
                                        </small>
                                    </div>
                                @elseif($product->isRemoved())
                                    <div class="progress-steps-caption">
                                        {{ $statusResult['label'] }}
                                        
                                        <small>
                                            <a href="">Disable</a>
                                        </small>
                                    </div>
                                @else
                                    <div class="progress-steps">
                                        <?php for($i=0; $i<$statusResult['actives']; $i++) { ?>
                                        <div class="progress-step active"></div>
                                        <?php } ?>
                                        
                                        <?php for($i=0; $i<3-$statusResult['actives']; $i++) { ?>
                                        <div class="progress-step"></div>
                                        <?php } ?>
                                    </div>
                                    
                                    <div class="progress-steps-caption">
                                        @if($statusResult['error'] != '')
                                        <button type="button"
                                            title="Notice"
                                            data-container="body"
                                            data-toggle="popover"
                                            data-placement="top"
                                            data-content="{{ $statusResult['error'] }}"
                                            data-trigger="hover"
                                            style="border:none; background-color:transparent;color:#6c7a86;">
                                            {{ $statusResult['label'] }}
                                        </button>
                                        @else
                                            <div class="progress-steps-caption">
                                                {{ $statusResult['label'] }}         
                                                
                                                <small>
                                                    <a href="{{ route('inventory.product.disable', array('id' => $product->id)) }}">Disable</a>
                                                </small>
                                            </div>   
                                        @endif
                                    </div>
                                @endif
                                
                                
                            </div>
                        </td>
                        <td>
                            @if($product->weight == 0)
                                <i class="glyphicon glyphicon-scale" style="color:#c4ccd0;padding:5px;cursor:pointer;font-size:16px;" title="There is no weight"></i>
                            @endif
                            
                            @if($product->categories->count() == 0)
                                <i class="glyphicon glyphicon-th-list" style="color:#c4ccd0;padding:5px;cursor:pointer;font-size:16px;" title="There is no category"></i>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">No products</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div>{!! $products->links() !!}</div>

        </div>
    </div>
@endsection

@section('page-scripts')
<script>
$(document).ready(function() {
    $('#status').change(function() {
        $('#inventory-form').submit();
    });
});
</script>
@endsection

