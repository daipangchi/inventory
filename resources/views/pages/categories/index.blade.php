@extends('master')

@section('content')
    <div class="page-categories-index">

        <div class="container-top">
            <h4>Categories</h4>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-sm-3">
                    @include('pages.categories.partials.categories-list', compact('categories'))
                </div>
                <div class="col-sm-9">
                    <h4>Create Category</h4>

                    <form action="/categories" method="POST">
                        @include('common.errors')
                        {{ csrf_field() }}

                        <!-------------------------------------------------------------------
                            CATEGORY NAME
                        -------------------------------------------------------------------->
                        <div class="form-group row{{ $errors->has('name') ? ' error' : '' }}">
                            <label class="col-sm-2 form-control-label">Category Name</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <input type="text" class="form-control" name="name" placeholder="Category Name"
                                           value="{{ old('name') }}">
                                </p>
                            </div>
                        </div>

                        <!-------------------------------------------------------------------
                            CATEGORY NAME HEBREW
                        -------------------------------------------------------------------->
                        <div class="form-group row{{ $errors->has('name_hebrew') ? ' error' : '' }}">
                            <label class="col-sm-2 form-control-label">Hebrew Name</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <input type="text" class="form-control" name="name_hebrew" placeholder="Hebrew Name"
                                           value="{{ old('name_hebrew') }}">
                                </p>
                            </div>
                        </div>

                        <!-------------------------------------------------------------------
                            CATEGORY DESCRIPTION
                        -------------------------------------------------------------------->
                        <div class="form-group row{{ $errors->has('description') ? ' error' : '' }}">
                            <label class="col-sm-2 form-control-label">Category Description</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <textarea type="text" class="form-control" name="description"
                                              placeholder="Category Description">{{ old('description') }}</textarea>
                                </p>
                            </div>
                        </div>

                        <!-------------------------------------------------------------------
                            CATEGORY FEE
                        -------------------------------------------------------------------->
                        <div class="form-group row{{ $errors->has('fee') ? ' error' : '' }}">
                            <label class="col-sm-2 form-control-label">Cadabra Category Fee</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <input type="text" class="form-control" name="fee" placeholder="00" value="{{ old('fee') }}">
                                </p>
                            </div>
                        </div>
                        
                        <!-------------------------------------------------------------------
                            CUSTOM CODE
                        -------------------------------------------------------------------->
                        <div class="form-group row{{ $errors->has('custom_code') ? ' error' : '' }}">
                            <label class="col-sm-2 form-control-label">Customs Code</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <input type="text" class="form-control" name="custom_code" placeholder="" value="{{ old('custom_code') }}">
                                </p>
                            </div>
                        </div>

                        <!-------------------------------------------------------------------
                            CATEGORY PARENT
                        -------------------------------------------------------------------->
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Parent Category</label>
                            <div class="col-sm-10">
                                <p id="category-parent" class="m-b-0">
                                    {{ old('parent_id') ? $categories->find(old('parent_id'))->name : 'To choose a parent category, please select one from the left side.' }}
                                </p>
                                <a id="remove-parent-button" class="text-danger" style="display: none;">
                                    <i class="glyphicon glyphicon-remove"></i>
                                    Remove
                                </a>
                                <input id="category-parent-input" type="hidden" name="parent_id" value="{{ old('parent_id') ?: '' }}">
                            </div>
                        </div>

                        {{--<div>--}}
                            {{--<hr>--}}
                            {{--@include('pages.categories.partials.taxes', ['taxes' => old('taxes')])--}}
                            {{--<hr>--}}
                            {{--@include('pages.categories.partials.deductions', ['deductions' => old('deductions')])--}}
                            {{--<hr>--}}
                        {{--</div>--}}

                        <div class="text-right">
                            <button class="btn btn-primary">Save Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
