@extends('master')

@section('content')
    <div class="page-inventory-create">

        <div class="container-top">
            <h4>Add Product</h4>
        </div>

        <div class="container">

            <form action="/inventory" method="post">
            {{ csrf_field() }}

            @include('common.errors')

                <!-------------------------------------------------------------------
                        PRODUCT NAME
                -------------------------------------------------------------------->
                <div class="form-group row{{ $errors->has('name') ? ' error' : '' }}">
                    <label class="col-sm-2 form-control-label">Product Name</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <input type="text" class="form-control" name="name" placeholder="Product Name" value="{{ old('name') }}">
                        </p>
                    </div>
                </div>

                <!-------------------------------------------------------------------
                        PRODUCT NAME
                -------------------------------------------------------------------->
                <div class="form-group row{{ $errors->has('description') ? ' error' : '' }}">
                    <label class="col-sm-2 form-control-label">Product Description</label>
                    <div class="col-sm-9">
                        <p class="form-control-static">
                            <textarea class="form-control" name="description" placeholder="Product Description">{{ old('description') }}</textarea>
                        </p>
                    </div>
                </div>

                <!-------------------------------------------------------------------
                    PRODUCT SKU
                -------------------------------------------------------------------->
                <div class="form-group row{{ $errors->has('sku') ? ' error' : '' }}">
                    <label class="col-sm-2 form-control-label">SKU</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <input type="text" class="form-control" name="sku" placeholder="SKU" value="{{ old('sku') }}">
                        </p>
                    </div>
                </div>

                <!-------------------------------------------------------------------
                    PARENT SKU
                -------------------------------------------------------------------->
                <div class="form-group row{{ $errors->has('parent_sku') ? ' error' : '' }}">
                    <label class="col-sm-2 form-control-label">Parent SKU</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <input type="text" class="form-control" name="parent_sku" placeholder="Parent SKU" value="{{ old('parent_sku') }}">
                        </p>
                    </div>
                </div>

                <!-------------------------------------------------------------------
                    CONDITION
                -------------------------------------------------------------------->
                <div class="form-group row">
                    <label for="condition" class="col-sm-2 form-control-label">Condition</label>
                    <div class="col-sm-3">
                        <p class="form-control-static">
                            <select id="condition" name="condition" class="form-control">
                                <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }} >New</option>
                                <option value="used" {{ old('condition') == 'used' ? 'selected' : '' }} >Used</option>
                                <option value="reconditioned" {{ old('condition') == 'reconditioned' ? 'selected' : '' }}>Reconditioned</option>
                            </select>
                        </p>
                    </div>
                </div>

                <!-------------------------------------------------------------------
                    MANUFACTURER
                -------------------------------------------------------------------->
                <div class="form-group row{{ $errors->has('manufacturer') ? ' error' : '' }}">
                    <label class="col-sm-2 form-control-label">Manufacturer</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <input type="text" class="form-control" name="manufacturer" placeholder="Manufacturer" value="{{ old('manufacturer') }}">
                        </p>
                    </div>
                </div>

                <!-------------------------------------------------------------------
                    MODEL NUMBER
                -------------------------------------------------------------------->
                <div class="form-group row{{ $errors->has('model_number') ? ' error' : '' }}">
                    <label class="col-sm-2 form-control-label">Model #</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <input type="text" class="form-control" name="model_number" placeholder="Model #" value="{{ old('model_number') }}">
                        </p>
                    </div>
                </div>

                <!-------------------------------------------------------------------
                    PRODUCT IDENTIFIER
                -------------------------------------------------------------------->
                <div class="form-group row{{ $errors->has('product_identifier') ? ' error' : '' }}">
                    <label class="col-sm-2 form-control-label">Product Identifier</label>
                    <div class="col-sm-4">
                        <p class="form-control-static">
                            <input type="text" class="form-control" name="product_identifier"
                                   placeholder="Product Identifier" value="{{ old('product_identifier') }}">
                        </p>
                    </div>

                    <div class="col-sm-2">
                        <p class="form-control-static">
                            <select class="form-control" name="product_identifier_type"
                                    aria-label="Product Identifier Type">
                                <option value="" {{ old('product_identifier_type') ? '' : 'selected' }}>
                                    Select Type
                                </option>
                                <option value="upc" {{ old('product_identifier_type') == 'upc' ? 'selected' : '' }}>
                                    UPC
                                </option>
                                <option value="ean" {{ old('product_identifier_type') == 'ean' ? 'selected' : '' }}>
                                    EAN
                                </option>
                                <option value="isbn" {{ old('product_identifier_type') == 'isbn' ? 'selected' : '' }}>
                                    ISBN
                                </option>
                            </select>
                        </p>
                    </div>
                </div>

                <!-------------------------------------------------------------------
                    MSRP
                -------------------------------------------------------------------->
                <div class="form-group row{{ $errors->has('msrp') ? ' error' : '' }}">
                    <label class="col-sm-2 form-control-label">MSRP</label>
                    <div class="col-sm-2">
                        <p class="form-control-static">
                            <input type="text" class="form-control" name="msrp" placeholder="MSRP" value="{{ old('msrp') }}">
                        </p>
                    </div>
                </div>

                <!-------------------------------------------------------------------
                    PRICE
                -------------------------------------------------------------------->
                <div class="form-group row{{ $errors->has('price') ? ' error' : '' }}">
                    <label class="col-sm-2 form-control-label">Price</label>
                    <div class="col-sm-2">
                        <p class="form-control-static">
                            <input type="text" class="form-control" name="price" placeholder="Price" value="{{ old('price') }}" step="any">
                        </p>
                    </div>
                </div>

                <!-------------------------------------------------------------------
                    WEIGHT
                -------------------------------------------------------------------->
                <div class="form-group row{{ $errors->has('weight') ? ' error' : '' }}">
                    <label class="col-sm-2 form-control-label">Weight</label>

                    <div class="col-sm-2">
                        <p class="form-control-static">
                            <input type="number" class="form-control" name="weight" placeholder="Weight"
                                   value="{{ old('weight') }}" step="any">
                        </p>
                    </div>

                    <div class="col-sm-2">
                        <p class="form-control-static">
                            <select class="form-control" name="weight_unit" aria-label="weight unit">
                                <option value="" {{ old('weight_unit') ? '' : 'selected' }}>Select Unit</option>
                                <option value="lbs" {{ old('weight_unit') === 'lbs' ? 'selected' : ''}}>pounds</option>
                                <option value="kg" {{ old('weight_unit') === 'kilograms' ? 'selected' : ''}}>kilograms
                                </option>
                            </select>
                        </p>
                    </div>
                </div>

                <!-------------------------------------------------------------------
                    PACKAGE DIMENSIONS
                -------------------------------------------------------------------->
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Package Dimensions</label>

                    <div class="col-sm-10">
                        <div class="row">
                            <div class="col-sm-2{{ $errors->has('height') ? ' error' : '' }}">
                                <p class="form-control-static">
                                    <input type="number" class="form-control" name="height" placeholder="Height"
                                           value="{{ old('height') }}" step="any">
                                </p>
                            </div>
                            <div class="col-sm-2{{ $errors->has('width') ? ' error' : '' }}">
                                <p class="form-control-static">
                                    <input type="number" class="form-control" name="width" placeholder="Width"
                                           value="{{ old('width') }}" step="any">
                                </p>
                            </div>
                            <div class="col-sm-2{{ $errors->has('length') ? ' error' : '' }}">
                                <p class="form-control-static">
                                    <input type="number" class="form-control" name="length" placeholder="Length"
                                           value="{{ old('length') }}" step="any">
                                </p>
                            </div>

                            <div class="col-sm-2{{ $errors->has('dimensions_unit') ? ' error' : '' }}">
                                <select class="form-control" name="dimensions_unit">
                                    <option value="" {{ old('dimensions_unit') ? '' : 'selected' }}>Select an option
                                    </option>
                                    <option value="inches" {{ old('dimensions_unit') === 'inches' ? 'selected' : ''}}>
                                        inches
                                    </option>
                                    <option value="centimeters" {{ old('dimensions_unit') === 'centimeters' ? 'selected' : ''}}>
                                        centimeters
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-------------------------------------------------------------------
                    ENABLE VARIATIONS
                -------------------------------------------------------------------->
                <div class="form-group row">
                    <div class="col-sm-10 col-sm-offset-2">
                        <div class="checkbox">
                            <input type="checkbox" name="variations_enabled" id="variations" {{ old('variations_enabled') ? 'checked' : '' }}>
                            <label for="variations">Variations</label>
                        </div>
                    </div>
                </div>

                <!-------------------------------------------------------------------
                    PRODUCT VARIATIONS
                -------------------------------------------------------------------->
                @include('pages.inventory.partials.create.attributes')
                @include('pages.inventory.partials.create.variations')

                <div class="text-right">
                    <button class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
@endsection
