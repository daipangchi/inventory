@extends('master')

@section('content')
    <div class="page-profile">

        <div class="container-top">
            <h4>Profile</h4>
        </div>

        <div class="container">

            <section class="tabs-section">
                <div class="tabs-section-nav tabs-section-nav-icons nav-tabs-justified">
                    <div class="tbl">
                        <ul class="nav" role="tablist">
                            <li class="nav-item">
                                <span class="nav-link active" href="#tabs-1-tab-1" role="tab" data-toggle="tab" aria-expanded="true">
                                    <span class="nav-link-in">
                                        <span class="glyphicon glyphicon-cog"></span>
                                        Account
                                    </span>
                                </span>
                            </li>
                            <li class="nav-item">
                                <span class="nav-link" href="#tabs-1-tab-3" role="tab" data-toggle="tab" aria-expanded="false">
                                    <span class="nav-link-in">
                                        <span class="glyphicon glyphicon-cog"></span>
                                        Shipping Info
                                    </span>
                                </span>
                            </li>
                            <li class="nav-item">
                                <span class="nav-link" href="#tabs-1-tab-2" role="tab" data-toggle="tab" aria-expanded="false">
                                    <span class="nav-link-in">
                                        <span class="glyphicon glyphicon-lock"></span>
                                        Password
                                    </span>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active in" id="tabs-1-tab-1" aria-expanded="false">
                        @include('pages.settings.partials.payments')
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-2" aria-expanded="true">
                        @include('pages.settings.partials.account')
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tabs-1-tab-3" aria-expanded="true">
                        @include('pages.settings.partials.shipping')
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
