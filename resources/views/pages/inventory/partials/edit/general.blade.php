<form action="/inventory/{{ $product->id }}" method="post">
{{ method_field('patch') }}
{{ csrf_field() }}

    <!-------------------------------------------------------------------
        PRODUCT NAME
    -------------------------------------------------------------------->
    <div class="form-group row{{ $errors->has('name') ? ' error' : '' }}">
        <label class="col-sm-2 form-control-label">Product Name</label>
        <div class="col-sm-9">
            <p class="form-control-static">
                <input type="text" class="form-control" name="name" placeholder="Product Name"
                       value="{{ $product->name }}">
                @if($product->parent_id && $product->parent)
                    <span class="text-muted">Parent product:
                        <a href="inventory/{{$product->parent->id}}/edit">{{$product->parent->name}}</a></span>
                @endif
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
                <textarea class="form-control" name="description" placeholder="Product Description">{{ old('description') ? old('description') : $product->content_description }}</textarea>
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
                    <option value="" {{ $product->condition ? '' : 'selected' }}>Select Condition</option>
                    <option value="new" {{ $product->condition == 'new' ? 'selected' : '' }} >New</option>
                    <option value="used" {{ $product->condition == 'used' ? 'selected' : '' }} >Used</option>
                    <option value="reconditioned" {{ $product->condition == 'reconditioned' ? 'selected' : '' }}>
                        Reconditioned
                    </option>
                </select>
            </p>
        </div>
    </div>

    <!-------------------------------------------------------------------
        QUANTITY
    -------------------------------------------------------------------->
    <div class="form-group row{{ $errors->has('quantity') ? ' error' : '' }}">
        <label class="col-sm-2 form-control-label">Quantity</label>
        <div class="col-sm-3">
            <p class="form-control-static">
                <input type="number" class="form-control" name="quantity" placeholder="Quantity"
                       value="{{ $product->quantity }}">
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
                <input type="text" class="form-control" name="manufacturer" placeholder="Manufacturer"
                       value="{{ $product->manufacturer }}">
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
                <input type="text" class="form-control" name="model_number" placeholder="Model #"
                       value="{{ $product->model_number }}">
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
                <input type="text" class="form-control" name="product_identifier" placeholder="Product Identifier"
                       value="{{ $product->product_identifier }}">
            </p>
        </div>

        <div class="col-sm-2">
            <p class="form-control-static">
                <select class="form-control" name="product_identifier_type" aria-label="Product Identifier Type">
                    <option value="" {{ $product->product_identifier_type ? '' : 'selected' }}>Select Type</option>
                    <option value="upc" {{ $product->product_identifier_type == 'upc' ? 'selected' : '' }}>UPC</option>
                    <option value="ean" {{ $product->product_identifier_type == 'ean' ? 'selected' : '' }}>EAN</option>
                    <option value="isbn" {{ $product->product_identifier_type == 'isbn' ? 'selected' : '' }}>ISBN</option>
                </select>
            </p>
        </div>
    </div>

    <!-------------------------------------------------------------------
        MSRP
    -------------------------------------------------------------------->
    <div class="form-group row{{ $errors->has('msrp') ? ' error' : '' }}">
        <label class="col-sm-2 form-control-label">MSRP</label>
        <div class="col-sm-3">
            <p class="form-control-static">
                <input type="text" class="form-control" name="msrp" placeholder="MSRP" value="{{ $product->msrp }}">
            </p>
        </div>
    </div>

    <!-------------------------------------------------------------------
        PRICE
    -------------------------------------------------------------------->
    <div class="form-group row{{ $errors->has('price') ? ' error' : '' }}">
        <label class="col-sm-2 form-control-label">Price</label>
        <div class="col-sm-3">
            <p class="form-control-static">
                <input type="text" class="form-control" name="price" placeholder="Price" value="{{ $product->price }}" step="any">
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
                <input type="text" class="form-control" name="weight" placeholder="Weight" value="{{ $product->weight }}" step="any">
            </p>
        </div>

        <div class="col-sm-2">
            <p class="form-control-static">
                <select class="form-control" name="weight_unit" aria-label="weight unit">
                    <option value="" {{ $product->weight_unit ? '' : 'selected' }}>Select Unit</option>
                    <option value="lbs" {{ $product->weight_unit === 'lbs' ? 'selected' : ''}}>pounds</option>
                    <option value="kg" {{ $product->weight_unit === 'kilograms' ? 'selected' : ''}}>kilograms</option>
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
                        <input type="number" class="form-control" name="height" placeholder="Height" value="{{ $product->height }}">
                    </p>
                </div>
                <div class="col-sm-2{{ $errors->has('width') ? ' error' : '' }}">
                    <p class="form-control-static">
                        <input type="number" class="form-control" name="width" placeholder="Width" value="{{ $product->width }}">
                    </p>
                </div>
                <div class="col-sm-2{{ $errors->has('length') ? ' error' : '' }}">
                    <p class="form-control-static">
                        <input type="number" class="form-control" name="length" placeholder="Length" value="{{ $product->length }}">
                    </p>
                </div>

                <div class="col-sm-2{{ $errors->has('dimensions_unit') ? ' error' : '' }}">
                    <select class="form-control" name="dimensions_unit">
                        <option value="" {{ $product->dimensions_unit ? '' : 'selected' }}>Select an option</option>
                        <option value="inches" {{ $product->dimensions_unit === 'inches' ? 'selected' : ''}}>inches
                        </option>
                        <option value="centimeters" {{ $product->dimensions_unit === 'centimeters' ? 'selected' : ''}}>
                            centimeters
                        </option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if($product->parent_id || $product->children->count() === 0)
        <div class="form-group row">
            <div class="col-sm-10 col-sm-offset-2">
                <hr>
                <h4>
                    Attributes

                    <button type="button" id="button-add-attribute" class="btn btn-sm btn-primary-outline">New</button>
                </h4>

                <div class="product-attributes-container">
                    @if($product->attributes && !empty($product->attributes))
                        <?php $i = 0; ?>
                        @foreach($product->attributes as $name => $value)
                            <div class="row form-group">
                                <div class="col-sm-4">
                                    <input type="text" class="form-control form-control-sm" name="attributes[{{ $i }}][name]" placeholder="Attribute" value="{{ $name }}">
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control form-control-sm" name="attributes[{{ $i }}][value]" placeholder="Value" value="{{ $value }}">
                                </div>
                                <div class="col-sm-4">
                                    <button type="button" class="btn btn-sm btn-danger-outline button-remove-attribute{{ $i == 0 ? ' hidden' : '' }}" data-row-number="{{ $i }}">Remove</button>
                                </div>
                            </div>
                            <?php $i++ ?>
                        @endforeach
                    @else
                        <div class="row form-group">
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="attributes[0][name]" placeholder="Attribute">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="attributes[0][value]" placeholder="Value">
                            </div>
                            <div class="col-sm-4">
                                <button type="button" class="btn btn-sm btn-danger-outline button-remove-attribute hidden" data-row-number="0">Remove</button>
                            </div>
                        </div>
                    @endif
                </div>

                <strong class="text-danger">Note: you must press the "Update Product" button to apply the removals/changes.</strong>
            </div>
        </div>
    @endif

    <hr>

    <div class="text-right">
        <button class="btn btn-primary">Update Product</button>
    </div>
</form>
