<form action="/inventory/{{ $product->id }}?_type=specs" method="post">
    <div class="specifications-container">
        @if($product->specs && ! empty($product->specs))
            <?php $i = -1; ?>
            @foreach($product->specs as $name => $values)
                <div class="row form-group spec">
                    <div class="col-sm-2">
                        <input type="text" name="specs[{{ ++$i }}][name]" value="{{ $name }}"
                               class="form-control form-control-sm spec-name"
                               placeholder="Name" aria-label="Spec name">
                    </div>
                    <div class="col-sm-9">
                        <select multiple name="specs[{{ $i }}][values][]" data-role="tagsinput" class="spec-values" placeholder="Values" aria-label="values">
                            @if(is_array($values) && count($values) > 0)   
                                @foreach($values as $value)
                                    @if(is_array($value) || is_object($value)) 
                                        <?php continue; ?>
                                    @else
                                        <option value="{{ $value }}" selected>{{ $value }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-sm-1">
                        <button class="btn btn-danger-outline btn-sm button-remove-spec">Remove</button>
                    </div>
                </div>
            @endforeach
            @else
            <div class="row form-group spec">
                <div class="col-sm-2">
                    <input type="text" name="specs[0][name]" aria-label="Spec name"
                           class="form-control form-control-sm spec-key" placeholder="Name">
                </div>
                <div class="col-sm-9">
                    <select multiple name="specs[0][values][]" data-role="tagsinput"
                            class="spec-values" placeholder="Values" aria-label="values"></select>
                </div>
                <div class="col-sm-1">
                    <button class="btn btn-danger-outline btn-sm button-remove-spec">Remove</button>
                </div>
            </div>
        @endif
    </div>

    <hr>

    <div class="text-xs-right">
        <strong class="m-r-3 text-danger">Note: you must press the "Save" button to apply the removals/changes.</strong>
        <button type="button" class="btn btn-default m-r-2" id="add-new-spec">Add New</button>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>

    {{ csrf_field() }}
    {{ method_field('patch') }}
</form>

<div class="row form-group spec" id="specs-input-template" style="display: none;">
    <div class="col-sm-2">
        <input type="text" name="specs[INDEX][name]" aria-label="Spec name"
               class="form-control form-control-sm spec-key" placeholder="Name">
    </div>
    <div class="col-sm-9">
        <select multiple name="specs[INDEX][values][]"
                class="spec-values" placeholder="Values" aria-label="values"></select>
    </div>
    <div class="col-sm-1">
        <button class="btn btn-danger-outline btn-sm button-remove-spec">Remove</button>
    </div>
</div>
