<div class="row">
    <h2 class="col-sm-6">Taxes</h2>
    <h2 class="col-sm-6 text-right">
        <button type="button" class="btn btn-primary-outline btn-sm pull-right" id="button-add-tax">
            <small class="glyphicon glyphicon-plus"></small>
            Add
        </button>
    </h2>
</div>

<div class="taxes">
    @if($taxes && count($taxes) && $taxes[0])
        @foreach($taxes as $index => $tax)
            <div class="row taxes-row {{ $index ? 'm-t-1' : '' }}">
                <div class="col-sm-4">
                    <select class="select-countries form-control form-control-sm" name="taxes[{{ $index }}][country_code]">
                        <option value>Select a country</option>
                        @foreach($countries as $code => $country)
                            <option value="{{ $code }}" {{ $tax['country_code'] == $code ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-4">
                    <div class="input-group input-group-sm">
                        <input type="number" min="0" class="form-control"
                               name="taxes[{{ $index }}][percentage]" placeholder="00"
                               value="{{ $tax['percentage'] }}" step="any">
                        <span class="input-group-addon">%</span>
                    </div>
                </div>

                <div class="col-sm-1">
                    <button type="button" class="btn btn-danger-outline btn-sm button-remove-tax"
                            data-category-id="{{ $category->id ?? '' }}"
                            data-category-tax-id="{{ $tax['id'] ?? '' }}">
                        <small class="glyphicon glyphicon-minus"></small>
                    </button>
                </div>
            </div>
        @endforeach
    @else
        <div class="row taxes-row">
            <div class="col-sm-4">
                <select class="select-countries form-control form-control-sm pull-right" name="taxes[0][country_code]">
                    <option value selected>Select a country</option>
                    @foreach($countries as $code => $country)
                        <option value="{{ $code }}">{{ $country }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-2">
                <div class="input-group input-group-sm">
                    <input type="number" min="0" class="form-control" name="taxes[0][percentage]"
                           placeholder="00">
                    <span class="input-group-addon">%</span>
                </div>
            </div>

            <div class="col-sm-1">
                <button type="button" class="btn btn-danger-outline btn-sm button-remove-tax hidden" disabled>
                    <small class="glyphicon glyphicon-minus"></small>
                </button>
            </div>
        </div>
    @endif
    <div class="clearfix"></div>
</div>
