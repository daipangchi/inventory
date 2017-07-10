@extends('master')

@section('content')
    <div class="page-categories-edit">
        <div class="container">
            <div class="row">
                <div class="col-sm-3">
                    @include('pages.categories.partials.categories-list', compact('categories'))
                </div>

                <div class="col-sm-9">
                    <h1>
                        {{ $category->name }} <small class="text-muted">- Edit Category</small>
                    </h1>

                    <form action="/categories/{{ $category->category_id }}" method="POST">
                        {!! csrf_field().method_field('patch') !!}

                        <!-------------------------------------------------------------------
                            CATEGORY NAME
                        -------------------------------------------------------------------->
                        <div class="form-group row{{ $errors->has('name') ? ' error' : '' }}">
                            <label class="col-sm-2 form-control-label">Category Name</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <input type="text" class="form-control" name="name" placeholder="Category Name" value="{{ old('name') ?: $category->name }}">
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
                                           value="{{ old('name_hebrew') ?: $category->name_hebrew }}">
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
                                    <textarea type="text" class="form-control" name="description" placeholder="Category Description">{{ old('description') ?: $category->description }}</textarea>
                                </p>
                            </div>
                        </div>

                        <!-------------------------------------------------------------------
                            CATEGORY FEE
                        -------------------------------------------------------------------->
                        <div class="form-group row{{ $errors->has('fee') ? ' error' : '' }}">
                            <label class="col-sm-2 form-control-label">Cadabra Fee</label>
                            <div class="col-sm-3">
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control" name="fee" placeholder="00" value="{{ old('fee') ?: number_format($category->fee, 2) }}" step="any">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-------------------------------------------------------------------
                            CUSTOM CODE
                        -------------------------------------------------------------------->
                        <div class="form-group row{{ $errors->has('custom_code') ? ' error' : '' }}">
                            <label class="col-sm-2 form-control-label">Customs Code</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <input type="text" class="form-control" name="custom_code" placeholder="" value="{{ old('custom_code') ?: $category->custom_code }}">
                                </p>           
                            </div>
                        </div>      

                        {{--<hr>--}}
                        {{--@include('pages.categories.partials.mappings', compact('category', 'amazonCategories', 'ebayCategories'))--}}

                        @if($category->level === 1)
                            <hr>
                            @include('pages.categories.partials.taxes', ['taxes' => $category->taxes->toArray()])
                        @endif
                        
                            <hr>
                            @include('pages.categories.partials.deductions', ['deduction' => $category->deduction ? $category->deduction->toArray() : []])
                        

                        @if($category->level <= 3)<hr>@endif

                        <div class="text-right">
                            <button type="button" id="delete-category-button" class="btn btn-danger-outline m-r-3" data-category-id="{{ $category->id }}">Delete</button>
                            <button type="submit" class="btn btn-primary">Update Category</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
