@extends('master')

@section('content')
    <div class="page-inventory-edit">
        <div class="container p-a-0 bg-none">
            @include('common.errors')
            <section class="tabs-section">
                <div class="tabs-section-nav">
                    <div class="tbl">
                        <ul class="nav" role="tablist">
                            <li class="nav-item">
                                <span class="nav-link active" href="#tabs-2-tab-1" role="tab" data-toggle="tab" aria-expanded="true">
									<span class="nav-link-in">General</span>
                                </span>
                            </li>
                            <li class="nav-item">
                                <span class="nav-link" href="#tabs-2-tab-2" role="tab" data-toggle="tab" aria-expanded="false">
									<span class="nav-link-in">Specifications</span>
                                </span>
                            </li>
                            <li class="nav-item">
                                <span class="nav-link" href="#tabs-2-tab-3" role="tab" data-toggle="tab" aria-expanded="false">
									<span class="nav-link-in">Images</span>
                                </span>
                            </li>
                            <li class="nav-item">
                                <span class="nav-link" href="#tabs-2-tab-4" role="tab" data-toggle="tab" aria-expanded="false">
                                    <span class="nav-link-in">Categories</span>
                                </span>
                            </li>
                            <li class="nav-item">
                                <span class="nav-link" href="#tabs-2-tab-5" role="tab" data-toggle="tab" aria-expanded="false">
									<span class="nav-link-in">History</span>
                                </span>
                            </li>
                            <li class="nav-item">
                                <span class="nav-link" href="#tabs-2-tab-6" role="tab" data-toggle="tab" aria-expanded="false">
									<span class="nav-link-in">Child Products{{ " ($product->variants)" }}</span>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="tab-content p-a-2">
                    <div role="tabpanel" class="tab-pane fade active in" id="tabs-2-tab-1" aria-expanded="true">
                        @include('pages.inventory.partials.edit.general', compact('products'))
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tabs-2-tab-2" aria-expanded="true">
                        @include('pages.inventory.partials.edit.specs', compact('product'))
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tabs-2-tab-3" aria-expanded="false">
                        @include('pages.inventory.partials.edit.images', compact('categories'))
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tabs-2-tab-4" aria-expanded="false">
                        @include('pages.inventory.partials.edit.categories')
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tabs-2-tab-5" aria-expanded="false">
                        @include('pages.inventory.partials.edit.history')
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tabs-2-tab-6" aria-expanded="false">
                        @include('pages.inventory.partials.edit.child-products')
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
